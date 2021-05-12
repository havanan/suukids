<?php


namespace App\Repositories;


use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\ShippingConfig;
use App\Models\Shop;
use App\Models\VTPOSTConfig;
use App\Models\VTPostLog;
use App\Models\VTPOSTStore;
use App\Models\Ward;
use Exception;
use Illuminate\Support\Facades\Log;
use DB;

class VTPostRepository extends BaseRepository
{
    public function __construct()
    {
    }

    function request($path, $method, $token = null, $params = []) {
        $client = new \GuzzleHttp\Client();
        $url = env('VTPOST_BASE_URL') . $path;
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Token' => $token
            ]
        ];

        if ($method == 'GET') {
            $options['query'] = $params;
        } else {
            $options['json'] = $params;
        }

        $response = $client->request($method, $url, $options);

        $body = json_decode($response->getBody());

        if ($body->status != 200) {
            $message = $body->message;
            throw new Exception('ViettelPost: '. $message);
        } else {
            return $body->data;
        }
    }

    function requestWithoutStatus($path, $method, $token = null, $params = []) {
        $client = new \GuzzleHttp\Client();
        $url = env('VTPOST_BASE_URL') . $path;
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Token' => $token
            ]
        ];

        if ($method == 'GET') {
            $options['query'] = $params;
        } else {
            $options['json'] = $params;
        }

        $response = $client->request($method, $url, $options);

        return json_decode($response->getBody());
    }

    public function login()
    {
        if (!empty(getCurrentUser())) {
            $vtpost_config = ShippingConfig::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
            $params = [
                'USERNAME' => $vtpost_config->vtpost_username,
                'PASSWORD' => $vtpost_config->vtpost_password
            ];
        } else {
            $params = [
                'USERNAME' => 'tungnt15ptit@gmail.com',
                'PASSWORD' => 'Kenlink@12'
            ];
        }

        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ];

        $url = env('VTPOST_BASE_URL') .'/user/Login';
        $options['body'] = json_encode($params);

        $response = $client->request('POST', $url, $options);

        $body = json_decode($response->getBody());
        session()->put('_vtpostinfo', ['token' => $body->data->token]);
        return $body->data->token;
    }

    public function getListProvinceByCityCode($cityCode = null) {
        if (empty($cityCode)) {
            return $this->request('/categories/listProvinceById', 'GET','', ['provinceId' => 0]);
        } else {
            return $this->request('/categories/listProvinceById', 'GET','', ['provinceId' => $cityCode]);
        }
    }

    public function getListDistrictByProvinceId($provinceId = null)
    {
        return $this->request('/categories/listDistrict', 'GET','', ['provinceId' => $provinceId]);
    }

    public function getListWardByDistrictId($districtId = null)
    {
        return $this->request('/categories/listWards', 'GET','', ['districtId' => $districtId]);
    }

    public function getListServices()
    {
        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }
        return $this->request('/categories/listService', 'POST',$token, ['TYPE' => 2]);
    }

    public function getListServicesExtend($serviceCode = null)
    {
        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }
        return $this->request('/categories/listServiceExtra', 'GET',$token, ['serviceCode' => $serviceCode]);
    }

    public function getListStore()
    {
        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }

        return $this->request('/user/listInventory', 'GET', $token);
    }

    public function createStore($data)
    {
        $inventory = [
            'PHONE' => $data['phone'],
            'NAME' => $data['name'],
            'ADDRESS' => $data['address'],
            'WARDS_ID' => $data['wards_id'],
        ];

        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }
        return $this->request('/user/registerInventory', 'POST',$token, $inventory);
    }

    public function sendVTPostOrder($orderData) {
        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }
        return $this->request('/order/createOrder', 'POST',$token, $orderData);
    }

    public function getPriceAll($data)
    {
        $info = session()->get('_vtpostinfo');
        if (isset($info)) {
            $token = $info['token'];
        } else {
            $token = $this->login();
        }

        return $this->requestWithoutStatus('/order/getPriceAll', 'POST', $token, $data);
    }


    public function createVTPostOrder($orderIds)
    {
        $vtpostConfig = VTPOSTConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
        if (empty($vtpostConfig)) {
            throw new Exception('Thông tin về VTPost chưa có, vui lòng thử lại');
        }

        $okOrders = [];
        $failOrders = [];

        foreach ($orderIds as $orderId) {
            $order = Order::query()->whereKey($orderId)->first();
            $orderProducts = $order->order_products;
            if (empty($orderProducts)) {
                $failOrder = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập sản phẩm'
                ];
                continue;
            }
            $listItem = [];
            $productName = '';
            $totalQuantity = 0;
            $totalWeight = 0;
            foreach ($orderProducts as $orderProduct) {
                $item = [];
                $product = Product::query()->whereKey($orderProduct->product_id)->first();
                $name = $orderProduct->quantity .' x '. $product->name;
                $productName .= empty($productName) ? $name : ", $name";

                $totalQuantity += $orderProduct->quantity;
                $totalWeight += $product->weight;
                $item['PRODUCT_NAME'] = $product->name;
                $item['PRODUCT_PRICE'] = (int) $product->price;
                $item['PRODUCT_WEIGHT'] = $orderProduct->weight;
                $item['PRODUCT_QUANTITY'] = $orderProduct->quantity;
                array_push($listItem, $item);
            }

            $okOrder = [];
            $okOrder['RECEIVER_FULLNAME'] = $order->customer->name;
            $okOrder['RECEIVER_PHONE'] = $order->phone;
            if (empty($order->province_id)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập tỉnh / thành phố'
                ];
                continue;
            }
            $province = Province::query()->whereKey($order->province_id)->first();
//            $vt_province = Province::query()->where('province_slug', 'like', '%'.$province->province_slug.'%')->whereNotNull('vtpost_province_id')->first();
            if (empty($province)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Lỗi thông tin tỉnh/ thành phố'
                ];
                continue;
            }
            $okOrder['RECEIVER_PROVINCE'] = $province->vtpost_province_id;
            $provinceArea = $this->provinceArea($province->vtpost_province_code);
            if (empty($order->district_id)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập quận / huyện'
                ];
                continue;
            }
            $district = District::query()->whereKey($order->district_id)->first();
//            $vt_district = District::query()->where('district_slug', 'like', '%'.$district->district_slug.'%')->where('_province_id', '=', $vt_province->vtpost_province_id)->whereNotNull('vtpost_district_id')->first();
            if (empty($district)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Lỗi thông tin quận / huyện'
                ];
                continue;
            }
            $okOrder['RECEIVER_DISTRICT'] = $district->vtpost_district_id;

            if (empty($order->ward_id)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập xã / phường'
                ];
                continue;
            }
            $ward = Ward::query()->whereKey($order->ward_id)->first();
//            $vt_ward = Ward::query()->where('ward_slug', 'like', '%'.$ward->ward_slug.'%')->whereNotNull('vtpost_ward_id')->first();
            if (empty($ward)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Lỗi thông tin xã / phường'
                ];
                continue;
            }
            $okOrder['RECEIVER_WARD'] = $ward->vtpost_ward_id;

            if (empty($order->customer) || empty($order->customer->address)) {
                $failOrders[] = [
                    'id' => $order->id,
                    'error_message' => 'Chưa nhập địa chỉ số nhà / đường'
                ];
                continue;
            }

            $okOrder['RECEIVER_ADDRESS'] = $order->customer->address;
            $okOrder['ORDER_NUMBER'] = $order->code;
            $okOrder['PRODUCT_NAME'] = $productName;
            $okOrder['PRODUCT_PRICE'] = (int) $order->total_price;
            $okOrder['PRODUCT_QUANTITY'] = $totalQuantity;
            if ($totalQuantity <= 2) {
                $okOrder['PRODUCT_WEIGHT'] = 100;
            } elseif ($totalQuantity <= 5) {
                $okOrder['PRODUCT_WEIGHT'] = 250;
            } elseif ($totalQuantity <= 10) {
                $okOrder['PRODUCT_WEIGHT'] = 500;
            } else {
                $okOrder['PRODUCT_WEIGHT'] = 1000;
            }
            $okOrder['PRODUCT_TYPE'] = 'HH';
            $okOrder['MONEY_COLLECTION'] = (int) $order->total_price;
            $okOrder['PRODUCT_DESCRIPTION'] = $order->shipping_note;
            $okOrder['checked'] = 1;
            $okOrder['fragile'] = 0;
            $okOrder['GROUPADDRESS_ID'] = $vtpostConfig->group_address_id;
            $storeInfo = VTPOSTStore::query()->where('group_address_id', '=', $okOrder['GROUPADDRESS_ID'])->first();
            $okOrder['SENDER_FULLNAME'] = $storeInfo->name;
            $okOrder['SENDER_ADDRESS'] = $storeInfo->address;
            $okOrder['SENDER_PHONE'] = $storeInfo->phone;
            $okOrder['SENDER_WARD'] = $storeInfo->ward_id;
            $okOrder['SENDER_DISTRICT'] = $storeInfo->district_id;
            $okOrder['SENDER_PROVINCE'] = $storeInfo->province_id;
            $okOrder['ORDER_PAYMENT'] = 3;
            $okOrder['MONEY_TOTAL'] = (int) $order->total_price;
            $okOrder['LIST_ITEM'] = $listItem;

            $okOrder['ORDER_NOTE'] = 'Cho xem hàng,'. $order->shipping_note;
            if ($provinceArea == 'HN') {
                $okOrder['ORDER_SERVICE'] = 'PHS';
            } elseif ($provinceArea == 'MB') {
                $okOrder['ORDER_SERVICE'] = 'LCOD';
            } else {
                $okOrder['ORDER_SERVICE'] = 'NCOD';
            }

//          check order service -> disable =============================================================================
            $infoRequestShip = [
                "SENDER_PROVINCE" => $okOrder['SENDER_PROVINCE'],
                "SENDER_DISTRICT" => $okOrder['SENDER_DISTRICT'],
                "RECEIVER_PROVINCE" => $okOrder['RECEIVER_PROVINCE'],
                "RECEIVER_DISTRICT" => $okOrder['RECEIVER_DISTRICT'],
                "PRODUCT_TYPE" => "HH",
                "PRODUCT_WEIGHT" => $okOrder['PRODUCT_WEIGHT'],
                "PRODUCT_PRICE" => $okOrder['PRODUCT_PRICE'],
                "MONEY_COLLECTION" => $okOrder['MONEY_COLLECTION'],
                "TYPE" => 1
            ];
            $listServicesPrice = $this->getPriceAll($infoRequestShip);
//            Log::info($listServicesPrice);
            foreach ($listServicesPrice as $service) {
                if ($service->MA_DV_CHINH == 'NCOD') {
                    $okOrder['ORDER_SERVICE'] = 'NCOD';
                }
            }
//            Log::info($okOrder);
//          end check ==================================================================================================

            array_push($okOrders, ['order_products' => $orderProducts, 'vtpost_data' => $okOrder, 'order' => $order]);
        }

        $successOrders = [];
        foreach ($okOrders as $okOrder) {
            $dbOrder = $okOrder['order'];
            try {
                $vtpostData = $okOrder['vtpost_data'];
                $response = $this->sendVTPostOrder($vtpostData);
                VTPostLog::query()->create([
                    'order_id' => $dbOrder->id,
                    'address' => $vtpostData['RECEIVER_ADDRESS'],
                    'product_name' => $vtpostData['PRODUCT_NAME'],
                    'price' => $vtpostData['MONEY_COLLECTION'],
                    'user_send' => getCurrentUser()->id,
                    'status' => 1,
                    'message' => json_encode($response),
                ]);
                $order = $okOrder['order'];
                $order['shipping_code'] = $response->ORDER_NUMBER;
                array_push($successOrders, ['ok_order' => $okOrder, 'vtpost_response' => $response]);
            } catch(Exception $e) {
                Log::debug('VTPost::createVTPostOrder()::'. $e->getMessage());
                $failOrders[] = [
                    'id' => $okOrder['order']['id'],
                    'error_message' => $e->getMessage()
                ];

                VTPostLog::query()->create([
                    'order_id' => $dbOrder->id,
                    'address' => $vtpostData['RECEIVER_ADDRESS'],
                    'product_name' => $vtpostData['PRODUCT_NAME'],
                    'price' => $vtpostData['MONEY_COLLECTION'],
                    'user_send' => getCurrentUser()->id,
                    'status' => 0,
                    'message' => $e->getMessage()
                ]);
            }
        }
        $successIds = [];
        DB::beginTransaction();
        try {
            foreach ($successOrders as $successOrder) {
                $okOrder = $successOrder['ok_order'];
                $response = $successOrder['vtpost_response'];
                $dbOrder = $okOrder['order'];
                $dbOrder->shipping_code = $response->ORDER_NUMBER;
                $dbOrder->save();
                array_push($successIds, $dbOrder->id);
            }
            DB::commit();
        } catch (Exception $e) {
            $successIds = [];
            Log::error('VTPost::createVTPostOrder()::' .$e);
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return [
            'oke_ids' => $successIds,
            'fail_data' => $failOrders
        ];
    }

    public function provinceArea($code)
    {
        $data = [
            'HN' => ['HNI'],
            'MB' => ['HBH', 'SLA', 'DBN', 'LCU', 'LCI', 'YBN', 'PHO', 'HGG', 'TQG', 'CBG', 'BKN', 'TNN', 'LSN', 'BGG', 'QNH', 'BNH', 'HNM', 'HDG', 'HPG', 'HYN', 'NDH', 'TBH', 'VPC', 'NBH', 'THA', 'NAN', 'HTH'],
            'MN' => ['QBH', 'QTI', 'HUE', 'DNG', 'QNM', 'QNI', 'BDH', 'PYN', 'KHA', 'NTN', 'BTN', 'KTM', 'GLI', 'DLK', 'DKG', 'LDG', 'HCM', 'VTU', 'BDG', 'BPC', 'DNI', 'TNH', 'AGG', 'BLU', 'BTE', 'CMU', 'CTO', 'DTP', 'HUG', 'KGG', 'LAN', 'STG', 'TGG', 'TVH', 'VLG']
        ];

        if (array_search($code, $data['HN']) !== false) {
            return 'HN';
        } elseif (array_search($code, $data['MB']) !== false) {
            return 'MB';
        } else {
            return 'MN';
        }
    }
}
