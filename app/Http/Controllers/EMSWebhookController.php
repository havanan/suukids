<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\EMSOrderStatus;
use App\Repositories\Admin\Sell\OrderRepository;
use App\Models\EMSWebhookLog;
use Illuminate\Support\Facades\Log;

class EMSWebhookController extends Controller {

    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function updateStatus(Request $request) {
        try {
            $shippingCode = $request->get('tracking_code');
            $orderCode = $request->get('order_code');
            $statusCode = $request->get('status_code');
            $emsTransaction = $request->header('ems-transaction');

            $webhookLogData = $request->all();
            $webhookLogData['json_body'] = json_encode($webhookLogData);
            $webhookLogData['ip'] = $request->ip();
            $webhookLogData['ems_transaction'] = $emsTransaction;
            EMSWebhookLog::create($webhookLogData);

            //Fake Data For Webhook
            if ($shippingCode == "EJ012345678VN" &&
                $orderCode == "OD-123456") {
                return response()->json(['code' => "success", "transaction" => $emsTransaction]);
            }
            // End Fake Data

            $order = Order::query()->where('code', $orderCode)->where('shipping_code', $shippingCode)->first();
            $emsStatus = EMSOrderStatus::query()->where('code', $statusCode)->first();

            if (empty($emsStatus)) {
                return response()->json(['code' => "error", "transaction" => $emsTransaction]);
            }

            if (empty($order)) {
                return response()->json(['code' => "error", "transaction" => $emsTransaction]);
            }

            if ($emsStatus->is_complete == 1) {
                $order->status_id = COMPLETE_ORDER_STATUS_ID;
                $order->save();
            }

            if ($emsStatus->is_refund == 1) {
                $order->status_id = REFUND_ORDER_STATUS_ID;
                $order->save();

                $this->orderRepository->increaseQuantityProduct($order);
            }

            return response()->json(['code' => "success", "transaction" => $emsTransaction]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['code' => "error", "transaction" => $emsTransaction]);
        }

    }
}
