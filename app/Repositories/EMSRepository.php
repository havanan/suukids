<?php

namespace App\Repositories;


use App\Models\EMSToken;
use App\Models\Order;
use App\Models\StockProduct;
use App\Models\EMSConfig;
use App\Models\EMSLog;
use Log;
use Exception;
use DB;

class EMSRepository extends BaseRepository {
    public function __construct() {

    }

    function request($path, $method, $param = []) {
        $client = new \GuzzleHttp\Client();
        $url = env('TRANSPORT_BASE_URL') . $path;
        if (!empty(getCurrentUser())){
            $emsToken = EMSToken::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
            $token = $emsToken->token;
        } else {
            $token = env('TRANSPOST_API_KEY');
        }
        $param['merchant_token'] = $token;

        $options = [
            'headers' => [
                'merchant_token' => $token,
                'accept' => 'application/json',
            ],
        ];

        if ($method == 'GET') {
            $options['query'] = $param;
        } else {
            $options['form_params'] = $param;
        }

        // dd($options);

        // dd(json_encode($param));
        $response = $client->request($method, $url, $options);
        $body = \json_decode((string)$response->getBody());
        $code = $body->code;

        if ($code == 'error') {
            $message = $body->message;
            throw new Exception('EMS: ' . $message);
        }

        if ($code != 'success') {
            throw new Exception('EMS Không rõ lỗi');
        }

        $data = $body->data;
        return $data;
    }

    public function getListStatus() {
        return $this->request('/metadata/status', 'GET');
    }

    public function getListProvince() {
        return $this->request('/address/province', 'GET');
    }

    public function getListDistrict() {
        return $this->request('/address/district', 'GET');
    }

    public function getListWard() {
        return $this->request('/address/ward', 'GET');
    }

    public function sendEMSOrder($orderData) {
        return $this->request('/orders/create', 'POST', $orderData);
    }

    public function createEmsOrder($orderIds) {
        $emsConfig = EMSConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
        if (empty($emsConfig)) {
            throw new Exception('Thông tin về EMS chưa có, vui lòng thử lại');
        }

        $okOrders = [];
        $failOrders = [];

        foreach ($orderIds as $orderId) {
            $order = Order::query()->whereKey($orderId)->first();
            $orderProducts = $order->order_products;
            if (empty($orderProducts)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập sản phẩm'
                ];
                continue;
            }

            $productName = [];
            $totalQuantity = 0;
            $totalWeight = 0;
            foreach ($orderProducts as $orderProduct) {
                $name = $orderProduct->quantity . " " . $orderProduct->product->name;
                $productName[] = $name;

                $totalQuantity += $orderProduct->quantity;
                $totalWeight += $orderProduct->weight;
            }

            $okOrder = [];
            $okOrder['to_name'] = $order->customer->name;
            $okOrder['to_phone'] = $order->phone;
            if (empty($order->province) || empty($order->province->ems_code)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập tỉnh thành'
                ];
                continue;
            }
            $okOrder['to_province'] = $order->province->ems_code . "";

            if (empty($order->district) || empty($order->district->ems_code)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập quận huyện'
                ];
                continue;
            }
            $okOrder['to_district'] = $order->district->ems_code . "";

            if (empty($order->ward) || empty($order->ward->ems_code)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập xã phường'
                ];
                continue;
            }
            $okOrder['to_ward'] = $order->district->ems_code . "";

            if (empty($order->customer) || empty($order->customer->address)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập địa chỉ'
                ];
                continue;
            }
            $okOrder['to_address']     = $order->customer->address;
            $okOrder['order_code']     = $order->code;
            $okOrder['product_name']   = implode(', ', $productName);
            $okOrder['total_amount']   = $order->total_price;
            $okOrder['total_quantity'] = $totalQuantity;
            $okOrder['total_weight']   = empty($totalWeight) ? 1 : $totalWeight;
            $okOrder['money_collect']  = $order->total_price;
            $okOrder['description']    = $order->shipping_note;
            $okOrder['checked']        = 1;
            $okOrder['fragile']        = 0;
            $okOrder['inventory']      = $emsConfig->inventory_id;
            $okOrder['service']        = $emsConfig->service_id;

            array_push($okOrders, ['order_products' => $orderProducts, 'ems_data' => $okOrder, 'order' => $order]);
        }

        $successOrders = [];

        foreach ($okOrders as $okOrder) {
            $dbOrder = $okOrder['order'];
            try {
                $emsData = $okOrder['ems_data'];

                $response = $this->sendEMSOrder($emsData);

                EMSLog::query()->create([
                    'order_id'     => $dbOrder->id,
                    'address'      => $emsData['to_address'],
                    'product_name' => $emsData['product_name'],
                    'price'        => $emsData['money_collect'],
                    'user_send'    => getCurrentUser()->id,
                    'status'       => 1,
                    'message'      => 'Thành công'
                ]);

                $order = $okOrder['order'];
                $order['shipping_code'] = $response->tracking_code;
                array_push($successOrders, ['ok_order' => $okOrder, 'ems_response' => $response]);
            } catch (Exception $e) {
                $failOrders[] = [
                    'id' => $okOrder['order']['id'],
                    'error_message' => $e->getMessage()
                ];

                EMSLog::query()->create([
                    'order_id'     => $dbOrder->id,
                    'address'      => $emsData['to_address'],
                    'product_name' => $emsData['product_name'],
                    'price'        => $emsData['money_collect'],
                    'user_send'    => getCurrentUser()->id,
                    'status'       => 0,
                    'message'      => $e->getMessage()
                ]);
            }
        }

        $successIds = [];
        DB::beginTransaction();
        try {
            foreach ($successOrders as $successOrder) {
                $okOrder = $successOrder['ok_order'];
                $response = $successOrder['ems_response'];
                $dbOrder = $okOrder['order'];
                $dbOrder->shipping_code = $response->tracking_code;
                $dbOrder->save();
                array_push($successIds, $dbOrder->id);
            }
            DB::commit();
        } catch (Exception $e) {
            $successIds = [];
            Log::error($e);
            DB::rollBack();
            // throw new Exception($e->getMessage());
        }

        return [
            'oke_ids' => $successIds,
            'fail_data' => $failOrders
        ];
    }

    public function getListInventory() {
        return $this->request('/inventory/list', 'GET');
    }

    public function getListService() {
        return $this->request('/metadata/service', 'GET');
    }
}
