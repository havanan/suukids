<?php

namespace App\Http\Controllers\Admin\Sell;

use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Repositories\CloudfoneRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderCloudfoneController extends Controller
{
    protected $cloudfoneRepository;
    /**
     * OrderController constructor.
     */
    public function __construct(CloudfoneRepository $cloudfoneRepository) {
        $this->cloudfoneRepository = $cloudfoneRepository;
    }
    
    public function historyCloudfoneIndex(Request $request) {
        $user = getCurrentUser();
        if (!$user->isAdmin()) {
            return \redirectIfNotHasPermission();
        }
        
        $params = $request->all();
        $phone = $params['phone'];
        $createFrom = null;
        $createTo = null;
        $currentPage = empty($params['page']) ? 1 : $params['page'];
        if (!empty($params['created_from'])) {
            $createFrom = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d H:i:s');
        }
        
        if (!empty($params['created_to'])) {
            $createTo = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d H:i:s');
        }
        
        try {
            $response = $this->cloudfoneRepository->getHistoryList($phone, $createFrom, $createTo, $currentPage);
            if (empty ($response)) {
                return view(VIEW_ADMIN_SELL_ORDER . 'cloudfone_history_index', compact('phone'));
            }
        
            $listHistory = $response->data;
            $total = $response->total;
            
            $paginator = new LengthAwarePaginator([], $total, 20, $currentPage, ['path' => route('admin.sell.order.call-history-cloudfone', $params)]);
            return view(VIEW_ADMIN_SELL_ORDER . 'cloudfone_history_index', compact('listHistory', 'phone', 'paginator'));
        } catch (\Exception $ex) {
            return view(VIEW_ADMIN_SELL_ORDER . 'cloudfone_history_index', compact('phone'));
        }
        
        return view(VIEW_ADMIN_SELL_ORDER . 'cloudfone_history_index', compact('phone'));
    }
    
    public function sendCallToCloudfone(Request $request) {
        try {
            $orderId = $request->get('order_id');
            $phoneIndex = $request->get('phone_index'); // Phone 1 hay 2
            
            $order = Order::query()->currentShop()->where('id', $orderId)->first();
            if (empty($order)) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => '????n h??ng kh??ng t???n t???i',
                ], HTTP_STATUS_BAD_REQUEST); 
            }
            
            $customer = $order->customer;
            $phone = $phoneIndex == 1 ? $customer->phone : $customer->phone2;
            
            if (empty($phone)) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => 'S??? ??i???n tho???i kh??ng t???n t???i',
                ], HTTP_STATUS_BAD_REQUEST); 
            }
            
            $user = \getCurrentUser();
            if (empty($user->active_cloudfone)) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => 'B???n kh??ng c?? quy???n g???i ??i???n',
                ], HTTP_STATUS_BAD_REQUEST); 
            }
            
            if (empty($user->cloudfone_code)) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => 'B???n ch??a ???????c thi???t l???p s??? n???i b??? b??n cloudfone',
                ], HTTP_STATUS_BAD_REQUEST); 
            }
            $this->cloudfoneRepository->makeACall($user->cloudfone_code, $phone, $customer, $order);
            
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => 'B???n th???c hi???n cu???c g???i th??nh c??ng, Vui l??ng ????? ?? ??i???n tho???i.',
            ], HTTP_STATUS_SUCCESS); 
            
        } catch (\Exception $ex) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST); 
        }
    }
}
