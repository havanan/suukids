<?php

namespace App\Http\Controllers\Admin\Sell;

use App\Models\ShippingConfig;
use App\Models\VTPOSTConfig;
use App\Repositories\VTPostRepository;
use Session;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductBundle;
use App\Helpers\Common;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Reminder;
use App\Models\ActionLog;
use App\Models\OrderType;
use App\Models\UserGroup;
use App\Models\OrderStatus;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Helpers\ExportLogHelper;
use Illuminate\Support\Facades\DB;
use App\Repositories\EMSRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CustomerRepository;
use App\Repositories\Admin\Sell\OrderRepository;
use App\Repositories\Admin\Profile\UserRepository;
use App\Repositories\Admin\OrderFirebaseRepository;
use App\Repositories\Admin\Sell\OrderSourceRepository;
use App\Repositories\Admin\Sell\OrderHistoryRepository;
use App\Repositories\Admin\Sell\OrderProductRepository;
use App\Repositories\Admin\Sell\DeliveryMethodRepository;
use App\Repositories\Admin\Warehouse\StockGroupRepository;
use App\Repositories\Admin\Product\ProductBundleRepository;

class OrderController extends Controller
{
    protected $deliveryMethodRepository;
    protected $productBundleRepository;
    protected $orderSourceRepository;
    protected $userRepository;
    protected $stockGroupRepository;
    protected $customerRepository;
    protected $orderRepository;
    protected $orderProductRepository;
    protected $orderHistoryRepository;
    protected $orderFirebaseRepository;
    protected $emsRepository;
    protected $vtpostRepository;

    /**
     * OrderController constructor.
     */
    public function __construct(DeliveryMethodRepository $deliveryMethodRepository,
        ProductBundleRepository $productBundleRepository,
        OrderSourceRepository $orderSourceRepository,
        UserRepository $userRepository,
        StockGroupRepository $stockGroupRepository,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
        OrderProductRepository $orderProductRepository,
        OrderHistoryRepository $orderHistoryRepository,
        OrderFirebaseRepository $orderFirebaseRepository, EMSRepository $emsRepository, VTPostRepository $vtpostRepository) {
        $this->deliveryMethodRepository = $deliveryMethodRepository;
        $this->productBundleRepository = $productBundleRepository;
        $this->orderSourceRepository = $orderSourceRepository;
        $this->userRepository = $userRepository;
        $this->stockGroupRepository = $stockGroupRepository;
        $this->customerRepository = $customerRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderFirebaseRepository = $orderFirebaseRepository;
        $this->emsRepository = $emsRepository;
        $this->vtpostRepository = $vtpostRepository;

        // Khi tạm dừng, hết hạn
        $this->middleware(function ($request, $next) {
            $shop = Shop::findOrFail(auth()->user()->shop_id);
            if ($shop->is_pause || ($shop->expired_date && strtotime($shop->expired_date) <= strtotime("now"))) {
                auth('users')->logout();
                return redirectIfNotHasPermission();
            }
            return $next($request);
        });

    }

    public function index()
    {
        $isIndexPage = true;
        $user = getCurrentUser();
        $needCall = 0;

        // Chỉ khi là Sale thì mới tính toán NeedCall, ko thì thôi
        if ($user->isOnlySale()) {
            $needCall = OrderProduct::query()->leftJoin('products', function ($join) {
                $join->on('order_products.product_id', '=', 'products.id');
            })->leftJoin('orders', function ($join) {
                $join->on('order_products.order_id', '=', 'orders.id');
            })->leftJoin('customers', function ($join) {
                $join->on('orders.customer_id', '=', 'customers.id');
            })->whereNotNull('orders.complete_date')->whereNotNull('products.customer_care_days')
                ->whereRaw(
                    'DATE_ADD(orders.complete_date, INTERVAL products.customer_care_days DAY) <= DATE_ADD(NOW(), INTERVAL (products.warning_days * order_products.quantity) DAY)')
                ->whereRaw(
                    'DATE_ADD(orders.complete_date, INTERVAL products.customer_care_days DAY) >= NOW()')
                ->where('order_products.called', 0)
                ->where(function ($subQuery) use ($user) {
                    if (!$user->isAdmin()) {
                        $subQuery->where('user_created', $user->id)
                            ->orWhere('upsale_from_user_id', $user->id)
                            ->orWhere('assigned_user_id', $user->id)
                            ->orWhere('close_user_id', $user->id)
                            ->orWhere('delivery_user_id', $user->id)
                            ->orWhere('user_created', $user->id)
                            ->orWhere('marketing_id', $user->id);
                    }
                })
                ->where('orders.shop_id', $user->shop_id)
                ->count();
        }
        $user_groups = UserGroup::query()->currentShop()->get();
        $orderSources = $this->orderSourceRepository->getAll();
        $deliveryMethods = $this->deliveryMethodRepository->getAll();
        $filterStatuses = $this->orderRepository->getFilterStatus();
        $productBundles = $this->productBundleRepository->all();
        $sales = $this->userRepository->getSales();
        $marketings = $this->userRepository->getMarketings();
        $users = $this->userRepository->getAll();
        $order_types = OrderType::all();
        $statuses = OrderStatus::query()->currentShop()->orderBy('level')->get();
        $statuses = $statuses->mapToGroups(function ($item, $key) {
            return [$item['level'] => $item];
        });

        $sortText = ORDER_TEXT;
//        $orderSortList = Setting::where('user_id', $user->id)->where('type', 1)->first();
        $orderSortList = ORDER_SORT_DEFAULT;
//        if (!empty($orderSortList)) {
//            $sortDefault = $orderSortList->content;
//        }
        $sortDefault = json_decode(ORDER_SORT_DEFAULT);
        // add location to table config
        $shopConfig = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
        if ($shopConfig->shipping == 'vtp') {
            $check1 = array_search((object)[
                'name' => 'location_vtp.location_currently',
                'show' => 1
            ], $sortDefault);
            $check2 = array_search((object)[
                'name' => 'location_vtp.location_currently',
                'show' => 0
            ], $sortDefault);

            if ($check1 == null && $check2 == null) {
                // add location by viettel post api
                $sortDefault[] = (object) [
                    'name' => 'location_vtp.location_currently',
                    'show' => 1
                ];
            }
        } else {
            $check1 = array_search((object)[
                'name' => 'location_ems.locate',
                'show' => 1
            ], $sortDefault);
            $check2 = array_search((object)[
                'name' => 'location_ems.locate',
                'show' => 0
            ], $sortDefault);
            if ($check1 == null && $check2 == null) {
                // add location by ems api
                $sortDefault[] = (object)[
                    'name' => 'location_ems.locate',
                    'show' => 1
                ];
            }
        }
        $isOnlySale = $user->isOnlySale();
        $phoneIndex = 0;
        $hasCallHistory = false;
        foreach ($sortDefault as $keySort => $item) {
            $sortDefault[$keySort]->sort = 0;
            foreach ($sortText as $keyText => $text) {
                if ($keyText == $item->name) {
                    $sortDefault[$keySort]->text = $text;
                }
                if ($item->name != 'stt' && $item->name != 'order_products' && $item->name != 'shop_name') {
                    $sortDefault[$keySort]->sort = 1;
                }
            }

            if ($item->name == 'customer.phone') {
                $phoneIndex = $keySort;
            }

            if ($item->name == 'customer.call.history') {
                $hasCallHistory = true;
            }

            if ($isOnlySale && $item->name == 'user_created_obj.name') {
                unset($sortDefault[$keySort]);
            }
        }

        // Them tuong lich su cuoc goi neu la phone
        if (!empty($phoneIndex) && empty($hasCallHistory) && ($user->isAdmin())) {
            $element = json_decode('{"name": "customer.call.history", "show": 1, "sort": 0, "text": "Lịch sử gọi"}');
            array_splice($sortDefault, $phoneIndex+1, 0, [$element]);
        }
        $productList = Product::onlyCurrentShop()->active()->get(['code', 'name'])->pluck('name', 'code');
        $data = compact(
            'productList',
            'orderSources',
            'filterStatuses',
            'deliveryMethods',
            'productBundles',
            'sales',
            'marketings',
            'users',
            'order_types',
            'statuses',
            'user_groups',
            'sortDefault',
            'needCall',
            'isIndexPage'
        );
        return view(VIEW_ADMIN_SELL_ORDER . 'index', $data);
    }

    public function getList(Request $request)
    {
        $params = $request->all();
        $paginate = Common::toPagination($params);
        $data = $this->orderRepository->getPaginate($params);
        $shop = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
        $user = getCurrentUser();

        // Log action
        if((isset($params['customer_phone_or_code']) && $params['customer_phone_or_code'] != "") || (isset($params['customer_name']) && $params['customer_name'] != "")){
            $params['uri'] = implode('/',array_slice(explode('/',request()->headers->get('referer')),3));
            $this->logAction($params, $shop);
        }
        if (!empty($data)) {
            $data->transform(function ($item) use ($shop, $user) {
                $item->shop_name = !empty($shop) ? $shop->name : '';
                if (!empty($item->customer)) {
                    $item->phone2 = $item->customer->phone2;
                    $item->phone = $item->customer->phone;
                    // $item->customer->phone = null;
                    // $item->customer->phone2 = null;
                }
                if (!$user->canViewFullPhoneOfOrder($item)) {
                    $item->phone = String2Stars($item->phone, 4, -3);
                    $item->phone2 = String2Stars($item->phone2, 4, -3);
                };

                //Hiển thị nút gọi hay không
                $item->show_call_btns = $user->isUsingCloudfone() && $user->canViewFullPhoneOfOrder($item);
                return $item;
            });
        }
        $data = Common::toJson($data);
        return $data;
    }

    private function logAction($params, $shop){
        $actionLog = new ActionLog();
        $actionLog->user_id         = getCurrentUser()->id;
        $actionLog->user_name       = getCurrentUser()->name;
        $actionLog->shop_id         = getCurrentUser()->shop_id;
        $actionLog->shop_name       = $shop->name;
        $actionLog->url             = $params['uri'];
        $actionLog->ip              = request()->ip();
        $actionLog->content_query   = json_encode(
            [
                'customer_phone_or_code' => isset($params['customer_phone_or_code']) ? $params['customer_phone_or_code'] : "",
                'customer_name' => isset($params['customer_name']) ? $params['customer_name'] : ""
            ],JSON_UNESCAPED_UNICODE);
        $actionLog->save();
    }

    public function getTotallRevenue(Request $request)
    {
        $params = $request->all();
        $data = $this->orderRepository->getTotallRevenue($params);
        return $data;
    }

    //Return Create View
    public function create(Request $request)
    {
        $closeWhenDone = $request->get('close_when_done');
        $deliveryMethods = $this->deliveryMethodRepository->getAll();
        $productBundles = $this->productBundleRepository->getAll();
        $orderSources = $this->orderSourceRepository->all();
        $users = $this->userRepository->getAll();
        $sales = $this->userRepository->getSales();
        $marketings = $this->userRepository->getMarketings();
        $stockGroups = $this->stockGroupRepository->all();

//      get address by shop shipping partner
        $shopInfo = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
        if ($shopInfo->shipping == 'vtp') {
            $provinces = Province::query()
//                ->whereNotNull('vtpost_province_code')
                ->where('status', 1)->get();
        } else {
            $provinces = Province::query()
//                ->whereNotNull('ems_code')
                ->where('status', 1)->get();
        }
//      end get address by shop shipping partner

        $statuses = OrderStatus::query()->currentShop()->orderBy('level')->get();
        $statuses = $statuses->mapToGroups(function ($item, $key) {
            return [$item['level'] => $item];
        });
        $order_types = OrderType::query()->currentShop()->get();
        $bundle_arr = ProductBundle::where('shop_id',auth()->user()->shop_id)->get();

        return view(VIEW_ADMIN_SELL_ORDER . 'create',
            compact('deliveryMethods', 'productBundles', 'orderSources', 'users', 'stockGroups', 'provinces', 'statuses', 'order_types', 'sales', 'marketings', 'closeWhenDone','bundle_arr')
        );
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $products = $request->get('order_product');
            $customerData = $request->get('customer');
            if (isset($customerData['tags'])) {
                $customerData['tags'] = implode(',',$customerData['tags']);
            }
            if (isset($customerData['id'])) {
                $customerDB = Customer::find($customerData['id']);
                $customerData['phone'] = $customerDB->phone;
            }
            if (empty($customerData['phone'])) {
                return $this->responseWithErrorMessage('Vui lòng nhập số điện thoại chính');
            }
            if (!preg_match('/(0)[0-9]{9,10}$/', $customerData['phone'])) {
                return $this->responseWithErrorMessage('Số điện thoại không đúng định dạng');
            }

            if (empty($customerData['name'])) {
                return $this->responseWithErrorMessage('Vui lòng nhập tên khách hàng');
            }

            // if (empty($products)) {
            //     return $this->responseWithErrorMessage('Vui lòng thêm sản phẩm');
            // }
            if (!empty($products)) {
                $products = array_filter($products, function ($item) {
                    return !empty($item['product_id']) && !empty($item['quantity']);
                });
            } else {
                $products = [];
            }

            // Order Data
            $orderData = $request->only('note1', 'note2', 'shipping_code', 'shipping_note', 'cancel_note',
                'is_top_priority', 'is_send_sms', 'is_inner_city',
                'status_id', 'shipping_service_id',
                'source_id', 'type', 'user_created', 'upsale_from_user_id',
                'assigned_user_id', 'cancel_note', 'discount_price', 'shipping_price',
                'other_price', 'province_id', 'district_id', 'ward_id');

            if (getCurrentUser()->isOnlySale() && empty($orderData['type'])) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => 'Vui lòng chọn tình trạng',
                ], HTTP_STATUS_BAD_REQUEST);
            }

            $orderData['phone'] = $customerData['phone'];
            $customer = $this->customerRepository->findOrCreate($customerData);
            $latest_order = Order::where('customer_id',$customer['customerId'])->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->count();
            $orderData['returned'] = $latest_order;
            $order = $this->orderRepository->create($customer['customerId'], $orderData, $products);
            /*
            if ($order->status_id == DELIVERY_ORDER_STATUS_ID) {
                $this->emsRepository->createEmsOrder([$order->id]);
            }
            */

//            $shopInfo = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
//            if ($order->status_id == DELIVERY_ORDER_STATUS_ID) {
//                if ($shopInfo->shipping == 'vtp') {
//                    $this->vtpostRepository->createVTPostOrder([$order->id]);
//                } elseif ($shopInfo->shipping == 'ems'){
//                    $this->emsRepository->createEmsOrder([$order->id]);
//                }
//            }

            foreach ($products as $product) {
                $this->orderProductRepository->create($product, $order->id);
            }
            $appointments = request()->input('appointment') ?: [];
            foreach ($appointments as $reminder) {
                Reminder::query()->create([
                    'order_id' => $order->id,
                    'created_by' => auth()->user()->id,
                    'time' => Carbon::createFromFormat('d/m/Y H:i', $reminder['time']),
                    'content' => $reminder['content'],
                ]);
            }
            $currentUser = getCurrentUser();
            $this->orderHistoryRepository->create([
                'order_id'   => $order->id,
                'type'       => 1,
                'message'    => ORDER_HISTORY_TYPE['1'],
                'created_by' => $currentUser->id,
            ]);

            DB::commit();

            if (in_array($order->status_id, SEND_NOTIFICATION_ORDER_STATUS_IDS)) {
                $this->orderFirebaseRepository->sendNotificationIfNeed([$order]);
            }

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã tạo đơn thành công",
                "url" => route('admin.sell.order.index'),
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $this->orderRepository->getById($id);
        $closeWhenDone = $request->get('close_when_done');
        if (!$this->canEditOrder($data)) {
            return redirectIfNotHasPermission();
        }

        /*
        $data->read = 1;
        $data->save();
         */
        $deliveryMethods = $this->deliveryMethodRepository->getAll();
        $productBundles = $this->productBundleRepository->all();
        $orderSources = $this->orderSourceRepository->getAll();
        $users = $this->userRepository->getAll();
        $sales = $this->userRepository->getSales();
        $marketings = $this->userRepository->getMarketings();
        $stockGroups = $this->stockGroupRepository->all();
        // get list province by shipping service

        $shopInfo = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
        if ($shopInfo->shipping == 'vtp') {
            $provinces = Province::query()->whereNotNull('vtpost_province_code')->where('status', 1)->get();
        } elseif ($shopInfo->shipping == 'ems') {
            $provinces = Province::query()->whereNotNull('ems_code')->where('status', 1)->get();
        }

        $statusesQuery = OrderStatus::query();
        $orderHistorys = $this->orderHistoryRepository->getByOrderId($id);

        $closeStatus = OrderStatus::query()->whereKey(DELIVERY_ORDER_STATUS_ID)->first();

        $user = getCurrentUser();
        if (!$user->isAdmin()) {
            $enableStatusIds = $user->getViewEnableStatusIds();
            $statusesQuery = $statusesQuery->whereIn('id', $enableStatusIds);
        }
        if (!$user->isAdmin() && $data->status->level >= $closeStatus->level) {
            $statusesQuery = $statusesQuery->where('level', '>', $closeStatus->level);
        }
        $statuses = $statusesQuery->orderBy('level')->currentShop()->get();
        $statuses = $statuses->mapToGroups(function ($item, $key) {
            return [$item['level'] => $item];
        });

        $statusEditable = false;
        if ($user->isAdmin()) {
            $statusEditable = true;
        }
        if (!empty($data->status) && $data->status->level < $closeStatus->level) {
            $statusEditable = true;
        }
        $bundle_arr = ProductBundle::where('shop_id',auth()->user()->shop_id)->get();
        $order_types = OrderType::query()->currentShop()->get();
        return view(VIEW_ADMIN_SELL_ORDER . 'edit', compact('data', 'deliveryMethods', 'productBundles', 'orderSources', 'users', 'stockGroups', 'provinces', 'statuses', 'order_types', 'sales', 'marketings', 'closeWhenDone', 'orderHistorys', 'statusEditable','bundle_arr'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->orderRepository->getById($id);
        if (!$this->canEditOrder($data)) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Bạn không có quyền cập nhật đơn hàng này",
            ], HTTP_STATUS_BAD_REQUEST);
        }

        $customer = Customer::find($data->customer_id);
        if ($customer->phone !== request()->input('customer.phone')) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Vui lòng không cập nhật số điện thoại. Tính năng này đã bị khóa!",
            ], HTTP_STATUS_BAD_REQUEST);
        }
        if (
            in_array(request()->input('status_id')*1,[DELIVERY_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
            && (auth()->user()->isSale() || auth()->user()->isMarketing())
        ) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Sales & MKT không được sửa trạng thái về Chuyển hàng, Thành công, Đã thu tiền!",
            ], HTTP_STATUS_BAD_REQUEST);
        }
        try {

            DB::beginTransaction();
            $products = $request->get('order_product');
            $customerData = $request->get('customer');
            if (isset($customerData['tags'])) {
                $customerData['tags'] = implode(',',$customerData['tags']);
            }
            if (!empty($products)) {
                $products = array_filter($products, function ($item) {
                    return !empty($item['product_id']) && !empty($item['quantity']);
                });
            } else {
                $products = [];
            }
            $orderData = $request->only('note1', 'note2', 'shipping_code', 'shipping_note', 'cancel_note', 'is_top_priority',
                'is_send_sms', 'is_inner_city', 'status_id', 'shipping_service_id',
                'source_id', 'type', 'user_created', 'upsale_from_user_id',
                'assigned_user_id', 'cancel_note', 'discount_price',
                'shipping_price', 'other_price', 'province_id', 'district_id', 'ward_id');
            if (getCurrentUser()->isOnlySale() && empty($orderData['type'])) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => 'Vui lòng chọn tình trạng',
                ], HTTP_STATUS_BAD_REQUEST);
            }

            $customerId = $data['customer_id'];
            $phone = !getCurrentUser()->isUsingCloudfone() ? $customerData['phone'] : $data->customer->phone;
            if (!preg_match('/(0)[0-9]{9,10}$/', $phone)) {
                return $this->responseWithErrorMessage('Số điện thoại không đúng định dạng');
            }
            $orderData['phone'] = $phone;
            $customerData['phone'] = $phone;
            $customer = $this->customerRepository->findOrCreate($customerData, $id);
            if (!empty($customer['updateData'])) {
                $this->orderHistoryRepository->createFromChanges($id, $customer['updateData']);
            }

            $customerId = $customer['customerId'];

            $updatedData = $this->orderRepository->update($id, $customerId, $orderData, $products, null);
            // Nếu trạng thái trước đó là xác nhận và đổi sang chuyển hàng ==> gọi sang ems
            /*
            if ($data->status_id != DELIVERY_ORDER_STATUS_ID && $orderData['status_id'] == DELIVERY_ORDER_STATUS_ID) {
                $data = $this->emsRepository->createEmsOrder([$data->id]);
                if (!empty($data) && !empty($data['fail_data'])) {
                    throw new \Exception($data['fail_data'][0]['error_message']);
                }
            }
            */

            // check shipping_partner in user table
            $shopInfo = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
            if ($data->status_id != DELIVERY_ORDER_STATUS_ID && $orderData['status_id'] == DELIVERY_ORDER_STATUS_ID) {
                //ko gọi sang vận đơn khi sửa từng đơn hàng
                /*
                if ($shopInfo->shipping == 'vtp') {
                    $data = $this->vtpostRepository->createVTPostOrder([$data->id]);
                    if (!empty($data) && !empty($data['fail_data'])) {
                        throw new \Exception($data['fail_data'][0]['error_message']);
                    }
                } elseif ($shopInfo->shipping == 'ems') {
                    $data = $this->emsRepository->createEmsOrder([$data->id]);
                    if (!empty($data) && !empty($data['fail_data'])) {
                        throw new \Exception($data['fail_data'][0]['error_message']);
                    }
                }
                */
            }

            if (!empty($updatedData)) {
                $this->orderHistoryRepository->createFromChanges($id, $updatedData);
            }

            $oldProduct = $data->order_products;
            $oldProductKey = [];
            foreach ($oldProduct as $key => $productItem) {
                $oldProductKey['order_product_' . $productItem['product_id'] . '_' . $productItem['stock_product_id']] = [
                    'product_name' => $productItem['product']['name'],
                    'quantity' => $productItem['quantity'],
                ];
            }

            $newProductKey = [];
            foreach ($products as $product) {
                $newProductKey['order_product_' . $product['product_id'] . '_' . $product['warehouse_id']] = [
                    'product_name' => $product['product_name'],
                    'quantity' => $product['quantity'],
                ];
            }
            $changes = [];
            $dataDiff = $this->compareMulti($oldProductKey, $newProductKey);
            if (!empty($dataDiff['diff'])) {
                $msg = '';
                foreach ($dataDiff['diff'] as $key => $diff) {
                    if (isset($diff['quantity'])) {
                        $msg .= 'Thay đổi số lượng sản phẩm: ' . $oldProductKey[$key]['product_name'] . ', số lượng từ: ' . $diff['quantity']['from'] . ' thành ' . $diff['quantity']['to'] . '. ';
                    }
                }

                $changes['update_product'] = $msg;
            }
            if (!empty($dataDiff['add'])) {
                $msg = 'Thêm sản phẩm: ';
                foreach ($dataDiff['add'] as $add) {
                    $msg .= $add['product_name'] . ', số lượng: ' . $add['quantity'] . '; ';
                }

                $changes['add_product'] = $msg;
            }
            if (!empty($dataDiff['remove'])) {
                $msg = 'Xóa sản phẩm: ';
                foreach ($dataDiff['remove'] as $remove) {
                    $msg .= $remove['product_name'] . ', số lượng: ' . $remove['quantity'] . '; ';
                }

                $changes['remove_product'] = $msg;
            }

            if (!empty($changes)) {
                $this->orderHistoryRepository->createFromChanges($id, $changes);
            }

            $this->orderProductRepository->deleteByOrderId($id);
            foreach ($products as $product) {
                $this->orderProductRepository->create($product, $id);
            }
            if ($data->status_id !== $orderData['status_id'] && $orderData['status_id'] == COMPLETE_ORDER_STATUS_ID) {
                $latest_order = Order::where('id', '<>', $id)->where('customer_id', $customerId)->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->count();
                Order::find($id)->update(['complete_date'=>date('Y-m-d H:i:s'),'returned' => $latest_order + 1]);
            }
            DB::commit();

            $order = Order::query()->onlyCurrentShop()->whereKey($id)->with('assigned_user')->first();
            if (!empty($order)) {
                if (in_array($order->status_id, SEND_NOTIFICATION_ORDER_STATUS_IDS)) {
                    $this->orderFirebaseRepository->sendNotificationIfNeed([$order]);
                }
            }

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã cập nhật đơn thành công",
                "url" => route('admin.sell.order.index'),
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    private function canEditOrder($order)
    {
        $user = getCurrentUser();
        return $user->canEditOrder($order);
    }

    public function updateStatus(Request $request)
    {
        $statusId = $request->get('status_id')*1;
        $user = getCurrentUser();
        if (!$user->canUpdatStatus($statusId)) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Bạn không có quyền thay đổi đến trạng thái này",
            ], HTTP_STATUS_BAD_REQUEST);
        }
        if (in_array($statusId,[DELIVERY_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID]) && ($user->isMarketing() || $user->isSale())) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Bạn không có quyền thay đổi đến trạng thái này",
            ], HTTP_STATUS_BAD_REQUEST);
        }
        DB::beginTransaction();
        try {
            $ids = $request->get('ids');

            // Nếu chuyển đơn hàng về trạng thái <= chuyển hàng
            // Nếu tìm thấy đơn nào đã chốt thì ko cho đổi trạng thái nữa
            $closeStatus = OrderStatus::query()->whereKey(DELIVERY_ORDER_STATUS_ID)->first();
            $status = OrderStatus::query()->whereKey($statusId)->first();
            if (!$user->isAdmin() && $status->level < $closeStatus->level) {
                $cantEdit = Order::query()->onlyCurrentShop()->whereIn('id', $ids)->whereHas('status', function ($q) use ($closeStatus) {
                    $q->where('level', '>=', $closeStatus->level);
                })->first();
                if (!empty($cantEdit)) {
                    return response()->json([
                        "code" => HTTP_STATUS_BAD_REQUEST,
                        "message" => "Có đơn đã chuyển hàng, không thể đổi trạng thái",
                    ], HTTP_STATUS_BAD_REQUEST);
                }
            }

            $okeIds = $ids;
            $notOkIds = [];
            $failData = [];
            $shopInfo = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
            if ($statusId == DELIVERY_ORDER_STATUS_ID) {
//                if ($shopInfo->shipping == 'vtp') {
//                    $data = $this->vtpostRepository->createVTPostOrder($ids);
//                    $okeIds = $data['oke_ids'];
//                    $failData = $data['fail_data'];
//                } elseif ($shopInfo->shipping == 'ems') {
//                    $data = $this->emsRepository->createEmsOrder($ids);
//                    $okeIds = $data['oke_ids'];
//                    $failData = $data['fail_data'];
//                }
            }

//            if ($statusId == DELIVERY_ORDER_STATUS_ID) {
//                $data = $this->emsRepository->createEmsOrder($ids);
//                $okeIds = $data['oke_ids'];
//                $failData = $data['fail_data'];
//            }

            foreach ($ids as $id) {
                if (!in_array($id, $okeIds)) {
                    array_push($notOkIds, $id);
                }
            }

            $updateDatas = $this->orderRepository->updateStatus($okeIds, $statusId, $closeStatus, $status);

            foreach ($updateDatas as $id => $updateData) {
                $this->orderHistoryRepository->createFromChanges($id, $updateData);
            }
            DB::commit();

            if (in_array($statusId, SEND_NOTIFICATION_ORDER_STATUS_IDS)) {
                $orders = Order::query()->onlyCurrentShop()->whereIn('id', $ids)->get();
                $this->orderFirebaseRepository->sendNotificationIfNeed($orders);
            }
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã cập nhật trạng thái thành công " . count($okeIds) . " đơn hàng",
                "fail_data" => $failData,
                "url" => route('admin.sell.order.index'),
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function flashShare(Request $request)
    {
        try {
            $param = $request->only('source_id', 'number', 'account_ids', 'group_id');
            $number = $param['number'];
            if (empty($number)) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => "Bạn chưa nhập số đơn cần chia",
                ], HTTP_STATUS_BAD_REQUEST);
            }

            if (empty($param['account_ids'])) {
                if (!empty($param['group_id'])) {
                    $sales = User::query()->where('user_group_id', $param['group_id'])->whereHas('permissions', function ($permissionQuery) {
                        $permissionQuery->where('sale_flag', 1);
                    })->get();
                } else {
                    $sales = $this->userRepository->getSales();
                }
            } else {
                $sales = User::query()->whereIn('id', $param['account_ids'])->orWhere(function ($subQuery) use ($param) {
                    if (!empty($param['group_id'])) {
                        $subQuery->where('user_group_id', $param['group_id']);
                    }

                    $subQuery->whereHas('permissions', function ($permissionQuery) {
                        $permissionQuery->where('sale_flag', 1);
                    });
                })->get();
            }

            $orders = $this->orderRepository->getNotAssignOrder($param);
            if ($number > $orders->count()) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => "Chỉ còn " . $orders->count() . " đơn chưa được gán",
                ], HTTP_STATUS_BAD_REQUEST);
            }

            if ($sales->isEmpty()) {
                return response()->json([
                    "code" => HTTP_STATUS_BAD_REQUEST,
                    "message" => "Nhóm bạn vừa chọn không có sales, vui lòng thử lại",
                ], HTTP_STATUS_BAD_REQUEST);
            }

            if (count($sales) > $number) {
                $sales = $sales->take($number);
            }
            $sales = $sales->toArray();
            $salesNumber = count($sales);
            DB::beginTransaction();

            $i = 0;
            foreach ($orders as $order) {
                $order->assigned_user_id = $sales[$i]['id'];
                $order->share_date = Carbon::now();
                $order->read = 0;
                $order->save();

                $i += 1;
                if ($i >= $salesNumber) {
                    $i = 0;
                }
            }
            DB::commit();

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã gán thành công " . $number . " đơn",
                "url" => route('admin.sell.order.index'),
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Có lỗi xảy ra vui lòng thử lại",
            ], HTTP_STATUS_BAD_REQUEST);
        }

    }

    public function countNotAssignOrder(Request $request)
    {
        $param = $request->only('source_id');
        return response()->json($this->orderRepository->countNotAssignOrder($param));
    }

    public function assignForSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderIds = $request->get('ids');
            $saleId = $request->get('sale_id');
            $currentUser = getCurrentUser();

            /* Thêm vào lịch sử đơn hàng */
            $this->orderRepository->assignOrdersForSale($orderIds, $saleId);

            $message = '';
            if ($saleId == -1) {
                $message = "Hủy chia đơn";
            } else {
                $sale = User::query()->whereKey($saleId)->first();
                $saleAccountId = !empty($sale) ? $sale->account_id : '';
                $message = "Chia đơn cho sale" . $saleAccountId;
            }

            foreach ($orderIds as $orderId) {
                $this->orderHistoryRepository->create([
                    'order_id' => $orderId,
                    'message' => $message,
                    'type' => 5,
                    'created_by' => $currentUser->id,
                ]);
            }
            /* Kết thúc thêm vào lịch sử */

            DB::commit();
            //SaleId = -1 là hủy gán đơn
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => $saleId != -1 ? "Bạn đã chia thành công " . count($orderIds) . " đơn" : "Đã hủy gán đơn",
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function assignForMarketing(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderIds = $request->get('ids');
            $marketingId = $request->get('marketing_id');
            $currentUser = getCurrentUser();

            $this->orderRepository->assignOrdersForMarketing($orderIds, $marketingId);

            /* Thêm vào lịch sử đơn hàng */
            $message = '';
            if ($marketingId == -1) {
                $message = "Hủy chia đơn";
            } else {
                $marketing = User::query()->whereKey($marketingId)->first();
                $accountId = !empty($marketing) ? $marketing->account_id : '';
                $message = "Chia đơn cho marketing " . $accountId;
            }

            foreach ($orderIds as $orderId) {
                $this->orderHistoryRepository->create([
                    'order_id' => $orderId,
                    'message' => $message,
                    'type' => 5,
                    'created_by' => $currentUser->id,
                ]);
            }
            /* Kết thúc thêm vào lịch sử */
            DB::commit();
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã chia thành công " . count($orderIds) . " đơn",
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function assignForGroup(Request $request)
    {
        try {
            $orderIds = $request->get('ids');
            $groupId = $request->get('group_id');
            $currentUser = getCurrentUser();

            $changes = $this->orderRepository->assignOrdersForGroup($orderIds, $groupId);
            foreach ($changes as $change) {
                $message = 'Chia đơn cho ' . $change['account_id'];
                $this->orderHistoryRepository->create([
                    'order_id' => $change['order_id'],
                    'message' => $message,
                    'type' => 5,
                    'created_by' => $currentUser->id,
                ]);
            }
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã chia thành công " . count($orderIds) . " đơn",
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function searchByPhone(Request $request)
    {
        try {
            $phone = $request->get('phone');
            if(!empty($phone)){
                $shop = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
                $uri = implode('/',array_slice(explode('/',request()->headers->get('referer')),3));
                $this->logAction(['customer_phone_or_code'=>$phone,'uri'=>$uri], $shop);
            }

            $orders = $this->orderRepository->searchByPhone($phone);
            return response()->json($orders, HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            return response()->json([], HTTP_STATUS_SUCCESS);
        }
    }
    public function searchByCustomerId(Request $request)
    {
        try {
            $customerId = $request->get('id');
            $customer = Customer::find($customerId);
            if(empty($customer)) {
                return response()->json([], HTTP_STATUS_SUCCESS);
            }
            $customer->phone = '';
            return response()->json($customer, HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            return response()->json([], HTTP_STATUS_SUCCESS);
        }
    }

    public function exportExcel(Request $request)
    {

        $ids = $request->get('ids');
        $type = $request->get('type');
        $query = $request->get('query');

        //Lấy ra danh sách order
        if (!empty($ids)) {
            $orders = $this->orderRepository->getByIds($ids, ['bundle', 'customer', 'province', 'order_products.product', 'status', 'close_user', 'delivery_user', 'user_created_obj', 'shipping_service', 'upsale_from_user']);
        } else {
            $orders = $this->orderRepository->getAll($query);
        }

        if (empty($orders)) {
            $orders = [];
        }

        $sortDefault = ORDER_SORT_DEFAULT;
        $sortText = ORDER_TEXT;

        $orderSortList = Setting::where('user_id', getCurrentUser()->id)->where('type', 1)->first();
        if (!empty($orderSortList)) {
            $sortDefault = $orderSortList->content;
        }
        $sortDefault = json_decode($sortDefault);

        foreach ($sortDefault as $keySort => $item) {
            foreach ($sortText as $keyText => $text) {
                if ($keyText == $item->name) {
                    $sortDefault[$keySort]->text = $text;
                }
            }
        }

        ExportLogHelper::addLogExportExcel('Xuất excel từ danh sách đơn hàng', 'Đã xuất excel từ danh sách đơn hàng vào lúc ' . Carbon::now(), url()->current(), $request->ip());

        switch ($type) {
            case 'order':
                return $this->exportExcelByOrder($orders, $sortDefault);
            case 'product':
                return $this->exportExcelByProduct($orders);
            default:
                return $this->exportTransportExcel($orders);
        }

        return ($type == 'order') ?: $this->exportExcelByProduct($orders);
    }

    private function exportExcelByOrder($orders, $sortDefault)
    {
        $file_name = 'donhang_' . Carbon::now()->timestamp;
        \Excel::create($file_name, function ($excel) use ($orders, $sortDefault) {
            $data = [];
            foreach ($orders as $key => $order) {
                $source = $order->source;
                $customer = $order->customer;
                $province = $order->province;
                $status = $order->status;
                $close_user = $order->close_user;
                $delivery_user = $order->delivery_user;
                $user_created = $order->user_created_obj;
                $shipping_service = $order->shipping_service;
                $upsale_from_user = $order->upsale_from_user;
                $orderProducts = $order->order_products;
                $productsTitle = "";
                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $orderProduct) {
                        if (!empty($orderProduct->product)) {
                            $productText = $orderProduct->quantity . " " . $orderProduct->product->name . " size " . $orderProduct->product->size;
                            $productsTitle .= !empty($productsTitle) ? ", $productText" : $productText;
                        }
                    }
                }

                $input = [];
                foreach ($sortDefault as $item) {
                    switch ($item->name) {
                        case 'stt':
                            $input[$item->text] = $key + 1;
                            break;
                        case 'assigned_user':
                            $input[$item->text] = $order->assigned_user ? $order->assigned_user->account_id : '';
                            break;
                        case 'code':
                            $input[$item->text] = $order->code;
                            break;
                        case 'shipping_code':
                            $input[$item->text] = $order->shipping_code;
                            break;
                        case 'source':
                            $input[$item->text] = $source ? $source->name : '';
                            break;
                        case 'customer':
                            $input[$item->text] = $customer ? $customer->name : '';
                            break;
                        case 'customer.phone':
                            $input[$item->text] = $customer ? ($customer->phone . ' ' . $customer->phone2) : '';
                            break;
                        case 'customer.address':
                            $input[$item->text] = $customer ? $customer->address : '';
                            break;
                        case 'order_products':
                            $orderProducts =
                            $input['Sản phẩm'] = $productsTitle;
                            break;
                        case 'note1':
                            $input[$item->text] = $order->note1;
                            break;
                        case 'note2':
                            $input[$item->text] = $order->note2;
                            break;
                        case 'status':
                            $input[$item->text] = $status ? $status->name : '';
                            break;
                        case 'total_price':
                            $input[$item->text] = $order->total_price;
                            break;
                        case 'close_user.account_id':
                            $input[$item->text] = $close_user ? $close_user->account_id : '';
                            break;
                        case 'close_date':
                            $input[$item->text] = $order->close_date;
                            break;
                        case 'delivery_user.account_id':
                            $input[$item->text] = $delivery_user ? $delivery_user->account_id : '';
                            break;
                        case 'delivery_date':
                            $input[$item->text] = $order->delivery_date;
                            break;
                        case 'user_created_obj.name':
                            $input[$item->text] = $user_created ? $user_created->name : '';
                            break;
                        case 'create_date':
                            $input[$item->text] = $order->create_date;
                            break;
                        case 'shipping_note':
                            $input[$item->text] = $order->shipping_note;
                            break;
                        case 'shipping_service.name':
                            $input[$item->text] = $shipping_service ? $shipping_service->name : '';
                            break;
                        case 'shop_name':
                            $input['Nguồn Upsale'] = $upsale_from_user ? $upsale_from_user->account_id : '';
                            break;
                        case 'upsale_from_user.account_id':
                            $input['Email KH'] = $customer ? $customer->email : '';
                            break;

                        default:
                            break;
                    }
                }

                array_push($data, $input);
            }
            $excel->sheet('Worksheet', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
            });

        })->download('xls');
    }

    private function exportExcelByProduct($orders)
    {
        $file_name = 'donhang_' . Carbon::now()->timestamp;
        \Excel::create($file_name, function ($excel) use ($orders) {
            $data = [];
            $index = 0;
            foreach ($orders as $key => $order) {
                if (empty($order->order_products)) {
                    continue;
                }

                $order_products = $order->order_products;
                $source = $order->source;
                $customer = $order->customer;
                $province = $order->province;
                $district = $order->district;
                $ward = $order->ward;
                $status = $order->status;
                $close_user = $order->close_user;
                $delivery_user = $order->delivery_user;
                $user_created = $order->user_created_obj;
                $shipping_service = $order->shipping_service;
                $upsale_from_user = $order->upsale_from_user;
                $type = $order->type_obj;

                $discountPercent = '';
                if (!empty($order->discount_price) && !empty($order->total_price)) {
                    $discountPercent = intval(100 * ($order->discount_price / $order->total_price));
                }

                foreach ($order_products as $order_product) {
                    if (empty($order_product->product)) {
                        continue;
                    }
                    $index += 1;
                    $unit = $order_product->product->productUnit;
                    $input = [
                        'STT' => $index,
                        'Tên KH' => $customer ? $customer->name : '',
                        'Mã Công ty' => '',
                        'Mã đơn hàng' => $order->code,
                        'Mã vận chuyển' => $order->shipping_code,
                        'Số điện thoại' => $customer ? ($customer->phone . ' ' . $customer->phone2) : '',
                        'Note chung' => $order->note1,
                        'Trạng thái' => $status ? $status->name : '',
                        'Tổng tiền' => $order->total_price,
                        'Mã hàng' => $order_product->product->code,
                        'Tên hàng' => $order_product->product->name,
                        'ĐVT' => $unit ? $unit->name : '',
                        'Số lượng' => $order_product->quantity,
                        'Đơn giá' => $order_product->price,
                        'Giảm giá(%)' => $discountPercent,
                        'Thành tiền' => $order_product->price * $order_product->quantity,
                        'Địa chỉ' => $customer ? $customer->address : '',
                        'Phường/Xã' => $ward ? $ward->_name : '',
                        'Quận/Huyện' => $district ? $district->_name : '',
                        'Tỉnh/Thành' => $province ? $province->_name : '',
                        'Note giao hàng' => $order->shipping_note,
                        'Xác nhận bởi' => $close_user ? $close_user->account_id : '',
                        'Giao hàng' => $shipping_service ? $shipping_service->name : '',
                        'Phân loại' => $type ? $type->name : '',
                        'Nguồn' => $source ? $source->name : '',
                        'Đơn' => '',
                        'Người tạo đơn' => $user_created ? $user_created->account_id : '',
                        'Nguồn Upsale' => $upsale_from_user ? $upsale_from_user->account_id : '',
                        'Đơn đã chia cho' => $order->assigned_user ? $order->assigned_user->account_id : '',
                        'Ngày tạo' => $order->create_date,
                        'Ngày chia' => $order->share_date,
                        'Ngày chốt' => $order->close_date,
                        'Ngày chuyển cho kế toán' => $order->assign_accountant_date,
                        'Ngày chuyển hàng' => $order->delivery_date,
                        'Ngày thành công' => $order->complete_date,
                        'Ngày thu tiền' => $order->collect_money_date,
                        'Ngày chuyển hoàn' => $order->refund_date,
                    ];
                    array_push($data, $input);
                }
            }
            $excel->sheet('Worksheet', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
            });

        })->download('xls');
    }

    private function exportTransportExcel($orders)
    {
        $file_name = 'donhang_' . Carbon::now()->timestamp;
        \Excel::create($file_name, function ($excel) use ($orders) {
            $data = [];
            foreach ($orders as $key => $order) {
                $order_products = $order->order_products;
                $source = $order->source;
                $customer = $order->customer;
                $province = $order->province;
                $district = $order->district;
                $ward = $order->ward;
                $status = $order->status;
                $close_user = $order->close_user;
                $delivery_user = $order->delivery_user;
                $user_created = $order->user_created_obj;
                $shipping_service = $order->shipping_service;
                $upsale_from_user = $order->upsale_from_user;
                $type = $order->type_obj;

                $input = [
                    'STT' => $key + 1,
                    'Tên KH' => $customer ? $customer->name : '',
                    'Mã Công ty' => '',
                    'Mã đơn hàng' => $order->code,
                    'Số điện thoại' => $customer ? ($customer->phone . ' ' . $customer->phone2) : '',
                    'Note chung' => $order->note1,
                    'Trạng thái' => $status ? $status->name : '',
                    'Tổng tiền' => $order->total_price,
                    'Sản phẩm' => $order->products_name,
                    'Địa chỉ' => $customer ? $customer->address : '',
                    'Phường / xã' => $ward ? $ward->_name : '',
                    'Quận / huyện' => $district ? $district->_name : '',
                    'Tỉnh / thành' => $province ? $province->_name : '',
                    'Note giao hàng' => $order->shipping_note,
                    'Xác nhận bởi' => $close_user ? $close_user->account_id : '',
                    'Ngày xác nhận' => $order->close_date,
                ];

                array_push($data, $input);
            }
            $excel->sheet('Worksheet', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
            });

        })->download('xls');
    }

    public function importExcel(Request $request)
    {
        $request->session()->forget('import_excel_order_data');
        $data = [];
        $statuses = OrderStatus::query()->currentShop()->orderBy('level')->get();
        $sales = $this->userRepository->getSales();
        return view(VIEW_ADMIN_SELL_ORDER . '.import', compact('data', 'statuses', 'sales'));
    }

    public function uploadImportExcel(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => 'File excel không tồn tai.',
            ], HTTP_STATUS_BAD_REQUEST);
        }

        try {
            $path = $request->file('excel_file')->getRealPath();
            $data = \Excel::load($path, function ($reader) {
                // $reader->ignoreEmpty();
            })->get();
            $headerRow = $data->first()->keys()->toArray();

            $countRows = $data->count();
            // if ($countRows > ORDER_LIMIT_COUNT_ROWS_IMPORT) {
            //     $messages = 'Nhập tối đa ' . ORDER_LIMIT_COUNT_ROWS_IMPORT . ' dòng';
            //     return $this->responseWithErrorMessage($messages);
            // }

            if ($headerRow !== Excel_ORDER_IMPORT_FORMAT) {
                return $this->responseWithErrorMessage('File nhập vào sai định dạng');
            }

            if ($data->count() <= 0) {
                return $this->responseWithErrorMessage('File nhập vào không có dữ liệu');
            }
            // bóc tách data từ file excel
            $response = $this->getItemExcel($data);

            $request->session()->put('import_excel_order_data', $response);

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "rows" => $countRows,
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $e) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $e->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function postImportExcel(Request $request)
    {
        $statusId = $request->get('status_id');
        $assignUserId = $request->get('assign_user_id');
        $response = $request->session()->get('import_excel_order_data');

        // dd($response);
        if ($response == null) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => 'Vui lòng thực hiện bước 1: Upload Excel',
            ], HTTP_STATUS_BAD_REQUEST);
        }
        // $accountIds = $response['users'];
        $orders = $response['orders'];
        $orderProducts = $response['order_products'];
        $productCodes = $response['products'];
        // $sourceNames= $response['sources'];
        // $provinceNames= $response['provinces'];
        // $deliveryMethodNames= $response['delivery_methods'];
        $customers = $response['customers'];
        $customerCollection = collect([]);
        try {
            // $users = User::query()->whereIn('account_id', $accountIds)->get();
            // $provinces = Province::query()->whereIn('_name', $provinceNames)->get();
            $products = Product::query()->currentShop()->whereIn('code', $productCodes)->get();
            // $sources = OrderSource::query()->whereIn('name', $sourceNames)->get();
            // $delivery_methods = DeliveryMethod::query()->whereIn('name', $deliveryMethodNames)->get();

            $orderData = [];
            foreach ($customers as $key => $customer) {
                $existCustomer = Customer::where('phone', $customer['phone'])->onlyCurrentShop()->first();
                if ($existCustomer) {
                    // Không fill nữa
                    // $existCustomer->fill($customer);
                    // $existCustomer->save();
                    $customerCollection->push($existCustomer);
                } else {
                    $newCustomer = new Customer();
                    $newCustomer->fill($customer);
                    $newCustomer->created_by = getCurrentUser()->id;
                    $newCustomer->save();
                    $customerCollection->push($newCustomer);
                }
            }

            /* Lọc và setup những thông tin cần thiết cho order */
            $okOrders = $this->filterAndSetupImportOrders($orders, $statusId, $assignUserId, $products, $customerCollection);
            $successImportCount = 0;
            $orderIds = [];

            foreach ($okOrders as $order) {
                DB::beginTransaction();
                try {

                    $orderProduct = isset($order['order_product']) ? $order['order_product'] : [];
                    /* Set Trùng đơn nếu có */
                    // $duplicateOrder = $this->orderRepository->getDuplicateOrderByPhone($order['phone']);
                    // if (!empty($duplicateOrder)) {
                    //     $order['duplicated'] = $duplicateOrder->id;
                    // }
                    $order['code'] = $this->orderRepository->generateCode();
                    $order = Order::create($order);
                    if (!empty($orderProduct)) {
                        $orderProduct['order_id'] = $order->id;
                        unset($order['order_product']);
                        OrderProduct::create($orderProduct);
                    }
                    DB::commit();
                    array_push($orderIds, $order->id);
                    $successImportCount += 1;
                } catch (\Exception $e) {
                    DB::rollBack();
                    dd($e);
                    continue;
                }
            }

            //  foreach ($okOrders as $order) {
            //     //Insert to database, bỏ qua những order có thông tin sao
            //     DB::beginTransaction();
            //     try {
            //         $orderProduct = isset($order['order_product']) ? $order['order_product'] : [];
            //         /* Set Trùng đơn nếu có */
            //         $duplicateOrder = $this->orderRepository->getDuplicateOrderByPhone($order['phone']);
            //         if (!empty($duplicateOrder)) {
            //             $order['duplicated'] = $duplicateOrder->id;
            //         }
            //         $order['code'] = $this->orderRepository->generateCode();
            //         $order = Order::create($order);
            //         if (!empty($orderProduct)) {
            //             $orderProduct['order_id'] = $order->id;
            //             unset($order['order_product']);
            //             OrderProduct::create($orderProduct);
            //         }
            //         DB::commit();
            //         array_push($orderIds, $order->id);
            //         $successImportCount += 1;
            //     } catch (\Exception $e) {
            //         DB::rollBack();
            //         continue;
            //     }
            // }

            $failCount = count($orders) - $successImportCount;
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "orderIds" => $orderIds,
                "message" => "Import thành công: $successImportCount dòng, thất bại: $failCount dòng",
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $e) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $e->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    /* Lọc và setup những thông tin cần thiết cho order */
    private function filterAndSetupImportOrders($orders, $statusId, $assignUserId, $products, $customerCollection)
    {
        //Lọc ra những order oke;
        $okOrders = [];
        foreach ($orders as $key => $order) {
            /* Setup thông tin cho order */
            $cus = $customerCollection->firstWhere('phone', $order['phone']);
            if (!empty($cus)) {
                $order['customer_id'] = $cus->id;
            } else {
                continue;
            }

            $order['status_id'] = $statusId;
            /* Người gán đơn */
            if (!empty($assignUserId)) {
                $order['assigned_user_id'] = $assignUserId;
            } else {
                $assigned_user = !empty($order['assigned_user_id']) ? User::findOrFail($order['assigned_user_id']) : null;
                if ($assigned_user) {
                    $order['assigned_user_id'] = $assigned_user->id;
                } else {
                    unset($orders[$key]['assigned_user_id']);
                }
            }
            /*
            // Người chốt
            $close_user = !empty($order['close_user_id']) ? $users->firstWhere('account_id', $order['close_user_id']) : null;
            if ($close_user) {
            $order['close_user_id'] = $close_user->id;
            } else {
            unset($order['close_user_id']);
            }
            // Người tạo
             */
            $user = getCurrentUser();
            $order['user_created'] = $user->id;
            $order['create_user_type'] = User::class;
            $order['create_date'] = '2020-05-30';

            // Nếu là marketing thì upsale là hắn luôn
            if ($user->isMarketing()) {
                $order['upsale_from_user_id'] = $user->id;
            }
            /*
            // Người chuyển
            $delivery_user = !empty($order['delivery_user_id']) ? $users->firstWhere('account_id', $order['delivery_user_id']) : null;
            if ($delivery_user) {
            $order['delivery_user_id'] = $delivery_user->id;
            } else {
            unset($order['delivery_user_id']);
            }
            // Tỉnh
            $province = $provinces->firstWhere('_name', $order['province_id']);
            if (!empty($province)) {
            $order['province_id'] = $province->id;
            } else {
            unset($order['province_id']);
            }

            // Nguồn
            $source = $sources->firstWhere('name', $order['source_id']);
            if (!empty($source)) {
            $order['source_id'] = $source->id;
            } else {
            unset($order['source_id']);
            }

            // Phươn thức vận chuyển
            $shipping_service = $delivery_methods->firstWhere('name', $order['shipping_service_id']);
            if (!empty($shipping_service)) {
            $order['shipping_service_id'] = $shipping_service->id;
            } else {
            unset($order['shipping_service_id']);
            }
             */
            if (!empty($order['order_product'])) {
                $orderProduct = $order['order_product'];

                if (!empty($orderProduct['title'])) {
                    if (strpos($orderProduct['title'], 'TUYẾN GIÁP')) {
                        $order['order_product']['product_id'] = 18;
                    }

                    if (strpos($orderProduct['title'], 'U Xơ')) {
                        $order['order_product']['product_id'] = 8;
                    }

                    if (strpos($orderProduct['title'], 'Mẫu Khang')) {
                        $order['order_product']['product_id'] = 11;
                    }

                    if (strpos($orderProduct['title'], 'An Giáp Vương')) {
                        $order['order_product']['product_id'] = 12;
                    }

                    if (strpos($orderProduct['title'], 'Im Boost')) {
                        $order['order_product']['product_id'] = 20;
                    }
                }

                /*
            $product = $products->firstWhere('code', $orderProduct['product_id']);
            if (!empty($product)) {
            $order['order_product']['product_id'] = $product->id;
            } else {
            unset($order['order_product']);
            // continue;
            }
             */
            }

            $order['shop_id'] = getCurrentUser()->shop_id;
            /* End Setup */
            array_push($okOrders, $order);
        }
        return $okOrders;
    }

    public function getItemExcel($data)
    {
        $response = [];
        $orders = [];
        $products = [];
        $order_products = [];
        // $sources = [];
        // $delivery_methods = [];
        // $users = [];
        // $provinces = [];
        $customers = [];

        // dd($data);

        foreach ($data as $key => $value) {
            $price = isset($value->gia_tien) ? (int) $value->gia_tien : 0;
            $quantity = isset($value->so_luong) ? (int) $value->so_luong : null;
            if (empty($quantity)) {
                // Lấy ra số lượng từ tên sản phẩm
                if (!empty($value->san_pham)) {
                    $nameArr = explode(" ", $value->san_pham);
                    if (count($nameArr) > 0 && is_numeric($nameArr[0])) {
                        $quantity = $nameArr[0];
                    }
                }
            }

            // Nếu lấy từ tên mà vẫn empty => set default -> 1
            if (empty($quantity)) {
                $quantity = 1;
            }

            $total_price = $price;

            $order = [
                'customer_id' => $value->so_dien_thoai,
                'discount_price' => 0,
                'shipping_price' => 0,
                'other_price' => 0,
                'share_date' => !empty($value->ngay_chia) ? (new Carbon($value->ngay_chia))->format('Y-m-d') : null,
                'close_date' => !empty($value->ngay_xac_nhan) ? (new Carbon($value->ngay_xac_nhan))->format('Y-m-d') : null,
                'create_date' => (new Carbon),
                'note1' => $value->note_chung,
                'note2' => $value->note_2,
                // 'province_id' => $value->tinh_thanh,
                'price' => $total_price,
                'source_id' => null,
                'shipping_service_id' => null,
                'total_price' => $total_price,
                'shipping_note' => null,
                'delivery_date' => !empty($value->ngay_chuyen) ? (new Carbon($value->ngay_chuyen))->format('Y-m-d') : null,
                'phone' => $value->so_dien_thoai,
                'created_at' => str_replace('\'', '', $value->ngay_tao),
            ];

            // if (!empty($value->nguoi_xac_nhan)) {
            //     $order['close_user_id'] = $value->nguoi_xac_nhan;
            //     $order['close_user_type'] = User::class;
            //     array_push($users, $value->nguoi_xac_nhan);
            // }

            // if (!empty($value->sale_duoc_chia)) {
            //     $order['assigned_user_id'] = $value->sale_duoc_chia;
            //     array_push($users, $value->sale_duoc_chia);
            // }

            // if (!empty($value->nguoi_tao)) {
            //     $order['create_user_id'] = $value->nguoi_tao;
            //     $order['create_user_type'] = User::class;
            //     array_push($users, $value->nguoi_tao);
            // }

            // if (!empty($value->nguoi_chuyen)) {
            //     $order['delivery_user_id'] = $value->nguoi_chuyen;
            //     $order['delivery_user_type'] = User::class;
            //     array_push($users, $value->nguoi_chuyen);
            // }

            $customer = [
                'phone' => $value->so_dien_thoai,
                'name' => !empty($value->ho_ten) ? $value->ho_ten : 'Không rõ',
                'address' => $value->dia_chi,
                'note' => $value->note_chung,
                'shop_id' => getCurrentUser()->shop_id,
            ];

            if (empty($value->so_dien_thoai)) {
                continue;
            }

            $order['phone'] = trim($value->so_dien_thoai);
            $phones = explode(" ", $order['phone']);

            if (count($phones) > 0) {
                $customer['phone'] = $phones[0];
                $order['phone'] = $phones[0];
            }

            if (count($phones) > 1) {
                $customer['phone2'] = $phones[1];
            }

            $productCode = !empty($value->ma_san_pham) ? trim($value->ma_san_pham) : '';
            $order_product = [
                'title' => $value->san_pham,
                'product_id' => $productCode,
                'price' => $price,
                'quantity' => $quantity,
            ];

            $order['order_product'] = $order_product;

            // array_push($sources, $value->nguon);
            // array_push($delivery_methods, $value->loai_ship);
            array_push($products, $productCode);
            array_push($order_products, $order_product);
            array_push($orders, $order);
            // array_push($provinces, $value->tinh_thanh);
            array_push($customers, $customer);
        }
        // Bỏ trường thừa trong mảng
        // $response['sources'] = array_unique($sources);
        // $response['delivery_methods'] = array_unique($delivery_methods);
        $response['products'] = array_unique($products);
        // $response['users'] = array_unique($users);
        // $response['provinces'] = array_unique($provinces);
        $response['orders'] = $orders;
        $response['order_products'] = $order_products;
        $response['customers'] = $customers;
        return $response;
    }

    public function quickEdit(Request $request)
    {
        $params = $request->only('search', 'start_date', 'end_date', 'status_id');
        $query = Order::query()->onlyCurrentShop();
        $user = getCurrentUser();
        /* Nếu không phải admin thì chỉ chỉnh sửa được đơn liên quan */
        if (!$user->isAdmin()) {
            $query = $query->where(function ($subQuery) use ($user) {
                $subQuery->where('user_created', $user->id)->orWhere('user_created', $user->id);
            });
        }

        /* Filter Query */
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query = $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone2', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($params['start_date'])) {
            $query = $query->where('create_date', '>=', Carbon::createFromFormat('d/m/Y', $params['start_date'])->format('Y-m-d'));
        }

        if (!empty($params['end_date'])) {
            $query = $query->where('create_date', '<=', Carbon::createFromFormat('d/m/Y', $params['end_date'])->format('Y-m-d'));
        }

        if (!empty($params['status_id'])) {
            $query = $query->where('status_id', $params['status_id']);
        }
        /* End Filter Query */
        $data = $query->paginate(10);

        $statuses = OrderStatus::query()->currentShop()->orderBy('level')->get();
        $users = $this->userRepository->all();

        if ($request->isMethod('POST')) {
            DB::beginTransaction();
            try {
                if (!empty($request->edit)) {
                    $dataOrder = [];
                    $dataCustomer = [];

                    $updatedStatusOrders = [];
                    foreach ($request->edit as $key => $item) {
                        $oldOrder = Order::query()->onlyCurrentShop()->whereKey($item['id'])->with('assigned_user')->first();
                        if (empty($oldOrder)) {continue;}
                        $newTotalPrice = str_replace(',', '', $item['total_price']);
                        $dataOrder[] = [
                            'id' => $item['id'],
                            'total_price' => $newTotalPrice,
                            'note1' => $item['note1'],
                            'close_user_id' => $item['close_user_id'],
                            'close_date' => !empty($item['close_date']) ? Carbon::createFromFormat('d/m/Y', $item['close_date']) : null,
                            'delivery_user_id' => $item['delivery_user_id'],
                            'delivery_date' => !empty($item['delivery_date']) ? Carbon::createFromFormat('d/m/Y', $item['delivery_date']) : null,
                            'status_id' => $item['status_id'],
                            'updated_at' => Carbon::now(),
                        ];

                        //Nếu trạng thái cần thông báo
                        if (!empty($item['status_id']) && in_array($item['status_id'], SEND_NOTIFICATION_ORDER_STATUS_IDS) && ($oldOrder->status_id != $item['status_id'])) {
                            $oldOrder->total_price = $newTotalPrice;
                            $oldOrder->status_id = $item['status_id'];
                            array_push($updatedStatusOrders, $oldOrder);
                        }

                        if (!isset($dataCustomer[$item['customer']['id']])) {
                            $dataCustomer[$item['customer']['id']] = [
                                'id' => $item['customer']['id'],
                                'name' => $item['customer']['name'],
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    $orders = new Order;
                    if (\Batch::update($orders, $dataOrder, 'id')) {
                        $customer = new Customer;
                        \Batch::update($customer, $dataCustomer, 'id');

                        DB::commit();
                        $this->orderFirebaseRepository->sendNotificationIfNeed($updatedStatusOrders);

                        return redirect()->route('admin.sell.order.quickEdit')->with('success', 'Sửa đơn hàng thành công');
                    }
                }
            } catch (\Exception $ex) {
                Log::error($ex);
                DB::rollBack();
                return back()->with('error', 'Thao tác thất bại');
            }
        }
        return view(VIEW_ADMIN_SELL_ORDER . '.quick_update', compact('data', 'statuses', 'users'));
    }

    public function deleteOrders(Request $request)
    {
        try {
            $orderIds = $request->get('ids');

            Order::query()->onlyCurrentShop()->whereIn('id', $orderIds)->delete();

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Bạn đã xóa thành công " . count($orderIds) . " đơn hàng đã chọn",
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }
    public function flashEdit(Request $request)
    {
        $id = $request->get('id');
        $info = $this->orderRepository->getById($id);
        $customer = $this->customerRepository->getById($info->customer_id);
        $product = $request->get('product');
        $products = $this->getProductFlashEdit($product, $id);
        DB::beginTransaction();
        try {
            // Lưu thông tin bảng orders
            $discount_price = convertPriceToInt(request('discount_price', 0));
            $quick_edit_price = convertPriceToInt(request('quick_edit_price', 0));
            $quick_edit_total_price = convertPriceToInt(request('quick_edit_total_price', 0));

            $info->note1 = $request->get('note1');
            $info->note2 = $request->get('note2');
            $info->discount_price = $discount_price;
            $info->total_price = $quick_edit_total_price;
            $info->price = $quick_edit_price;
            $info->save();
            //Lưu thông tin bảng customers
            if ($request->get('address') != null) {
                $customer->address = $request->get('address');
                $customer->save();
            }

            //Xóa hết bản ghi cũ trong bảng order_products theo order_id
            $this->orderProductRepository->deleteByOrderId($id);
            //Thêm mới vào bảng order_products
            foreach ($products as $product) {
                $this->orderProductRepository->create($product, $id);
            }
            DB::commit();
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => 'Bạn đã cập nhật thành công đơn hàng',
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }
    public function getProductFlashEdit($products, $order_id)
    {
        $data = [];
        if (empty($products)) {
            return $data;
        }
        foreach ($products as $item) {
            $value = explode('_', $item['name']);
            if (count($value) == 2) {
                $data[$value[1]]['order_id'] = $order_id;
                $data[$value[1]]['product_id'] = $value[1];
                $data[$value[1]]['warehouse_id'] = STOCK_OUT_PRODUCT;
                if ($value[0] == 'quantity') {
                    $data[$value[1]]['quantity'] = convertPriceToInt($item['value']);
                }
                if ($value[0] == 'price') {
                    $data[$value[1]]['price'] = convertPriceToInt($item['value']);
                }
            }
        }
        return $data;
    }

    public function commonSearch(Request $request)
    {
        try {
            $phone = $request->get('phone');
            if (empty($phone)) {
                return [
                    'results' => [],
                    'pagination' => [],
                ];
            }

            $data = Order::query()->with('customer')->whereHas('customer', function ($q) use ($phone) {
                $q->where('phone', 'LIKE', '%' . $phone . '%');
            })->onlyCurrentShop()->paginate(10)->items();
            return response()->json([
                'results' => array_map(function ($order) {
                    return [
                        'id' => $order['id'],
                        'text' => $order['code'] . ' sdt: ' . $order['customer']['phone'] . '-' . $order['customer']['name'],
                        'phone' => $order['customer']['phone'],
                    ];
                }, $data),
                "pagination" => [
                    "more" => false,
                ],
            ]);
            return $data;
        } catch (\Exception $ex) {
            return [
                'results' => [],
                'pagination' => [],
            ];
        }
    }

    public function getWaitingOrders()
    {
        $user = getCurrentUser();
        $waitingOrders = Order::query()->onlyCurrentShop()->where('assigned_user_id', $user->id)->where('read', 0)->get();
        return view(VIEW_ADMIN_SELL_ORDER . 'waiting_orders', compact('waitingOrders'));
    }

    public function saveOrderSort(Request $request)
    {
        try {
            $dataSort = json_encode($request['sort']);
            $sortUser = Setting::where('user_id', getCurrentUser()->id)->where('type', 1)->first();
            if (!empty($sortUser)) {
                $sortUser->content = $dataSort;
                $sortUser->timestamps = false;
                if ($sortUser->save()) {
                    return response()->json([
                        "code" => HTTP_STATUS_SUCCESS,
                        "message" => 'Bạn đã thay đổi danh sách sắp xếp thành công',
                    ], HTTP_STATUS_SUCCESS);
                }
            } else {
                $sortUser = new Setting;
                $sortUser->user_id = getCurrentUser()->id;
                $sortUser->content = $dataSort;
                $sortUser->type = 1;
                $sortUser->timestamps = false;

                if ($sortUser->save()) {
                    return response()->json([
                        "code" => HTTP_STATUS_SUCCESS,
                        "message" => 'Bạn đã thay đổi danh sách sắp xếp thành công',
                    ], HTTP_STATUS_SUCCESS);
                }
            }

            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $ex->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function getOrderHistory(Request $request)
    {
        $id = $request->get('order_id');
        $orderHistoryList = $this->orderHistoryRepository->getByOrderId($id);

        return response()->json([
            "data" => $orderHistoryList,
        ], HTTP_STATUS_SUCCESS);
    }
    public function getInfo(Request $request)
    {
        $id = $request->get('id');
        $orderHistoryList = $this->orderRepository->findWithProductCustomer($id);
        return response()->json([
            "data" => $orderHistoryList,
        ], HTTP_STATUS_SUCCESS);
    }

    private function compareMulti($array1, $array2)
    {
        $result = [
            'remove' => [],
            'add' => [],
            'diff' => [],
        ];

        foreach ($array1 as $k => $v) {
            if (is_array($v) && isset($array2[$k]) && is_array($array2[$k])) {
                $sub_result = $this->compareMulti($v, $array2[$k]);
                foreach (array_keys($sub_result) as $key) {
                    if (!empty($sub_result[$key])) {
                        $result[$key] = array_merge_recursive($result[$key], array($k => $sub_result[$key]));
                    }
                }
            } else {
                if (isset($array2[$k])) {
                    if ($v != $array2[$k]) {
                        $result["diff"][$k] = array("from" => $v, "to" => $array2[$k]);
                    }
                } else {
                    $result["remove"][$k] = $v;
                }
            }
        }
        foreach ($array2 as $k => $v) {
            if (!isset($array1[$k])) {
                $result["add"][$k] = $v;
            }
        }
        return $result;
    }

    public function importExcelCollectMoney () {
        return view(VIEW_ADMIN_SELL_ORDER . '.import-collect-money');
    }

    public function postImportExcelCollectMoney(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => 'File excel không tồn tai.',
            ], HTTP_STATUS_BAD_REQUEST);
        }

        DB::beginTransaction();
        try {
            $path = $request->file('excel_file')->getRealPath();
            $data = \Excel::load($path, function ($reader) {
                $reader->ignoreEmpty();
            })->get();
            $headerRow = $data->first()->keys()->toArray();
            $countRows = $data->count();
            if ($countRows > ORDER_LIMIT_COUNT_ROWS_IMPORT) {
                $messages = 'Nhập tối đa ' . ORDER_LIMIT_COUNT_ROWS_IMPORT . ' dòng';
                return $this->responseWithErrorMessage($messages);
            }

            if ($headerRow !== Excel_ORDER_IMPORT_COLLECT_MONEY_FORMAT) {
                return $this->responseWithErrorMessage('File nhập vào sai định dạng');
            }

            if ($data->count() <= 0) {
                return $this->responseWithErrorMessage('File nhập vào không có dữ liệu');
            }

            $successCount = 0;
            foreach ($data as $key => $value) {
                $shippingCode = $value->ma_vd;
                $date_str = $value->ngay_thu_tien;

                if (empty($shippingCode) || empty ($date_str)) {
                    continue;
                }
                try {
                    $date = Carbon::createFromFormat('d/m/Y H:i:s', $date_str);
                } catch (\Exception $e) {
                    $date = Carbon::createFromFormat('d/m/Y H:i', $date_str);
                }

                $order = Order::query()->onlyCurrentShop()->where('shipping_code', $shippingCode)->first();

                if (empty($order)) {
                    continue;
                }

                $order->status_id = COLLECT_MONEY_ORDER_STATUS_ID;
                $order->collect_money_date = $date;
                $order->save();
                $successCount = $successCount + 1;
            }

            DB::commit();

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "rows" => $successCount,
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $e->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    public function importExcelBillWay()
    {
        return view(VIEW_ADMIN_SELL_ORDER . '.import-billway', compact('data', 'statuses', 'sales'));
    }

    public function postImportExcelBillWay(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => 'File excel không tồn tai.',
            ], HTTP_STATUS_BAD_REQUEST);
        }

        DB::beginTransaction();
        try {
            $path = $request->file('excel_file')->getRealPath();
            $data = \Excel::load($path, function ($reader) {
                $reader->ignoreEmpty();
            })->get();
            $headerRow = $data->first()->keys()->toArray();
            $countRows = $data->count();
            if ($countRows > ORDER_LIMIT_COUNT_ROWS_IMPORT) {
                $messages = 'Nhập tối đa ' . ORDER_LIMIT_COUNT_ROWS_IMPORT . ' dòng';
                return $this->responseWithErrorMessage($messages);
            }

            if ($headerRow !== Excel_ORDER_IMPORT_BILLWAY_FORMAT) {
                return $this->responseWithErrorMessage('File nhập vào sai định dạng');
            }

            if ($data->count() <= 0) {
                return $this->responseWithErrorMessage('File nhập vào không có dữ liệu');
            }

            $successCount = 0;
            foreach ($data as $key => $value) {
                $code = $value->ma;
                $shippingCode = $value->ma_vd;
                $statusName = $value->trang_thai;
                if (empty($code) || empty($statusName)) {
                    continue;
                }

                $order = Order::query()->onlyCurrentShop()->where('code', $code)->first();
                $status = OrderStatus::query()->currentShop()->where('name', $statusName)->first();

                if (empty($order) || empty($shippingCode)) {
                    continue;
                }

                $order->shipping_code = $shippingCode;
                if (!empty($status)) {
                    $order->status_id = $status->id;

                    //Ngày chốt
                    if ($status->id == CLOSE_ORDER_STATUS_ID) {
                        $order->close_user_id = $user->id;
                        $order->close_user_type = User::class;
                        $order->close_date = Carbon::now();
                    }
                    //Ngày chuyển hàng
                    if ($status->id == DELIVERY_ORDER_STATUS_ID) {
                        $order->delivery_user_id = $user->id;
                        $order->delivery_user_type = User::class;
                        $order->delivery_date = Carbon::now();
                    }
                    //Ngày Thành công
                    if ($status->id == COMPLETE_ORDER_STATUS_ID) {
                        $order->complete_date = Carbon::now();
                    }
                    //Ngày Thu tiền
                    if ($status->id == COLLECT_MONEY_ORDER_STATUS_ID) {
                        $order->collect_money_date = Carbon::now();
                    }

                    //Ngày Chuyển hoàn
                    if ($status->id == REFUND_ORDER_STATUS_ID) {
                        $order->refund_date = Carbon::now();
                    }

                    //Ngày hủy
                    if ($status->id == CANCEL_ORDER_STATUS_ID) {
                        $order->cancel_date = Carbon::now();
                        $order->cancel_user_id = $user->id;
                        $order->cancel_user_type = User::class;
                    }
                }

                $order->save();

                $successCount = $successCount + 1;
            }

            DB::commit();

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "rows" => $successCount,
            ], HTTP_STATUS_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $e->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    function getOrdersByIds(Request $request) {
        $ids = $request->get('ids');
        $orders = $this->orderRepository->getByIds($ids, ['customer', 'bundle', 'user_created_obj', 'status', 'province', 'district']);
        $orders = $orders->map(function ($order) {
            $order->edit_url = route('admin.sell.order.edit', $order->id);
            return $order;
        });
        return response()->json($orders);
    }
}
