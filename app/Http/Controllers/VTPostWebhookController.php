<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\VTPOSTWebHookLog;
use App\Repositories\Admin\Sell\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;

class VTPostWebhookController extends Controller
{
    protected $orderRepository;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    public function updateStatus(Request $request)
    {
        try {
//            $transaction = $request->header('transaction');
            $transaction = round(microtime(true) * 1000);
            $dataAll = $request->all();
            $data = $dataAll['DATA'];
            $data['transaction'] = $transaction;

            $webHookData = [
                'ip' => $request->ip(),
                'order_number' => $data['ORDER_NUMBER'],
                'order_reference' => $data['ORDER_REFERENCE'],
                'order_statusdate' => $data['ORDER_STATUSDATE'],
                'order_status' => $data['ORDER_STATUS'],
                'location_currently' => $data['LOCALION_CURRENTLY'],
                'note' => $data['NOTE'],
                'product_weight' => $data['PRODUCT_WEIGHT'],
                'json_body' => json_encode($data)
            ];

            VTPOSTWebHookLog::create($webHookData);
            $orderId = (string) $data['ORDER_REFERENCE'];
            $orderInfo = Order::query()->where('code', '=', $orderId)->first();

            if ($data['ORDER_STATUS'] >= 300 && $data['ORDER_STATUS'] <= 500) {
                if ($orderInfo->status_id != DELIVERY_ORDER_STATUS_ID) {
                    $orderInfo->status_id = DELIVERY_ORDER_STATUS_ID;
                    $orderInfo->shipping_code = $data['ORDER_NUMBER'];
                    $orderInfo->save();
                    Log::info('Hook::CHANGE_STATUS_SHIPPING_CODE::order_code=='. $orderInfo->code.'--hook_code=='.$orderId);
                }
            }
            // create order_histories
            OrderHistory::query()->create([
                'order_id' => $orderInfo->id,
                'type' => 5,
                'message' => 'WEBHOOK::STATUS='.$data['ORDER_STATUS'].'-DATE::'.$data['ORDER_STATUSDATE'].'-Vị trí:: '. $data['LOCALION_CURRENTLY'],
            ]);
            // end create order_histories

            $order = Order::query()->where('shipping_code', '=', $data['ORDER_NUMBER'])->first();
            if ($order) {
                if ($data['ORDER_STATUS'] == 501) {
                    $order->status_id = COMPLETE_ORDER_STATUS_ID;
                    $order->complete_date = Carbon::createFromFormat('d/m/Y H:i:s',$data['ORDER_STATUSDATE']);
                    $latest_order = Order::where('id', '<>', $order->id)->where('customer_id', $order->customer_id)->where('bundle_id',$order->bundle_id)->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->count();
                    $order->returned = $latest_order + 1;
                    $order->save();
                }

                if ($data['ORDER_STATUS'] == 504) {
                    $order->status_id = REFUND_ORDER_STATUS_ID;
                    $order->save();

                    $this->orderRepository->increaseQuantityProduct($order);
                }
            }

            return response()->json([
                'code' => 'success',
                'message' => 'success',
                'transaction' => $transaction
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'code' => 'error',
                'message' => $exception->getMessage(),
                'transaction' => $transaction
            ]);
        }
    }
}
