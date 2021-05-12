<?php

namespace App\Repositories\Admin\Sell;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\StockProduct;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LogIsOldCustomer;
use Log;

class OrderRepository extends BaseRepository
{

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function getPaginate($param)
    {
        $query = $this->getListQuery($param);
        return $query->paginate($param['limit']);
    }

    public function getAll($param)
    {
        return $this->getListQuery($param)->currentShop()->get();
    }

    public function getTotallRevenue($param)
    {
        $query = $this->getListQuery($param);
        $countOrder = $query->count();
        $countAmount = $query->sum('total_price');
        return ['countOrder' => $countOrder, 'countAmount' => $countAmount];
    }

    private function getListQuery($param)
    {
        $user = \getCurrentUser();
        $dates = !empty($param['dates']) ? $param['dates'] : [];
        $status = !empty($param['status']) ? $param['status'] : [];
        $shopInfo = Shop::query()->whereKey($user->shop_id)->first();
        if ($shopInfo->shipping == 'vtp') {
            $query = $this->model::query()->with(['assigned_user', 'upsale_from_user','bundle', 'source', 'customer',
                'shipping_service', 'status', 'order_products.product',
                'user_created_obj', 'close_user', 'delivery_user', 'location_vtp']);
        } else {
            $query = $this->model::query()->with(['assigned_user', 'upsale_from_user','bundle', 'source', 'customer',
                'shipping_service', 'status', 'order_products.product',
                'user_created_obj', 'close_user', 'delivery_user', 'location_ems']);
        }
        foreach ($dates as $key => $date) {
            $from = Carbon::createFromFormat('d/m/Y', $date['from'])->startOfDay();
            $to = Carbon::createFromFormat('d/m/Y', $date['to'])->endOfDay();
            $query = $query->whereBetween($key, [$from, $to]);
        }

        if (isset($param['returned'])&&strlen($param['returned'])) {
            $query = $query->where('returned', $param['returned']);
        }
        if (!empty($param['source_id'])) {
            $query = $query->where('source_id', $param['source_id']);
        }

        if (!empty($param['shipping_service_id'])) {
            $query = $query->where('shipping_service_id', $param['shipping_service_id']);
        }

        if (!empty($param['assigned_user_id'])) {
            $assignUserId = $param['assigned_user_id'];
            if ($assignUserId == -1) {
                $query = $query->where(function ($q) use ($assignUserId) {
                    $q->whereNull('assigned_user_id')
                        ->orWhereNull('user_created');
                });
            } else {
                $query = $query->where(function ($q) use ($assignUserId) {
                    $q->where('assigned_user_id', $assignUserId)
                        ->orWhere('user_created', $assignUserId);
                });
            }

        }

        if (!empty($param['marketing_id'])) {
            $marketingId = $param['marketing_id'];
            $query = $query->where(function ($q) use ($marketingId) {
                $q->where('upsale_from_user_id', $marketingId)
                    ->orWhere('marketing_id', $marketingId)
                    ->orWhere('user_created', $marketingId);
            });
        }

        if (!empty($param['staff_id'])) {
            $staffId = $param['staff_id'];
            $query = $query->where(function ($q) use ($staffId) {
                $q->where('user_created', $staffId)
                    ->orWhere('upsale_from_user_id', $staffId)
                    ->orWhere('assigned_user_id', $staffId)
                    ->orWhere('close_user_id', $staffId)
                    ->orWhere('delivery_user_id', $staffId)
                    ->orWhere('user_created', $staffId)
                    ->orWhere('marketing_id', $staffId);
            });
        }

        // Không cho phép SALE và MKT xem đơn hàng đã thành công.
        // if (getCurrentUser()->isOnlyMarketing() || getCurrentUser()->isOnlySale()) {
        //     $query = $query->whereNotIn('status_id', STATUS_AN_DON_HANG_SALE_MKT);
        // }

        if (!empty($param['bundle_id'])) {
            switch ($param['bundle_id']) {
                case -1:
                    //Phân loại
                    $query = $query->whereNotNull('bundle_id');
                    break;
                case -2:
                    //Chưa phân loại
                    $query = $query->whereNull('bundle_id');
                    break;
                case -3:
                    //Chưa phân loại
                    $query = $query->where('is_old_customer', '=', ACTIVE);
                    break;
                default:
                    $query = $query->where('bundle_id', $param['bundle_id']);
                    break;
            }
        }

        if (!empty($param['product_code'])) {
            $searchProductIds = Product::query()->where('code', 'LIKE', '%' . $param['product_code'] . '%')->get()->pluck('id');
            $orderIds = OrderProduct::query()->whereIn('product_id', $searchProductIds)->select('order_id')->get()->pluck('order_id');
            $query = $query->whereIn('id', $orderIds);
        }

        if (!empty($param['customer_phone_or_code'])) {
            $phoneOrCode = trim($param['customer_phone_or_code']);
            $query = $query->where(function ($subQuery) use ($phoneOrCode) {
                $subQuery->where('code', 'LIKE', '%' . $phoneOrCode . '%')->orWhereHas('customer', function ($q) use ($phoneOrCode) {
                    $q->where('phone', 'LIKE', '%' . $phoneOrCode . '%')
                        ->orWhere('phone2', 'LIKE', '%' . $phoneOrCode . '%');
                })->orWhere('shipping_code', 'LIKE', '%' . $phoneOrCode . '%');
            });
        }

        if (!empty($param['customer_name'])) {
            $query = $query->whereHas('customer', function ($subQuery) use ($param) {
                $subQuery->where('name', 'LIKE', '%' . $param['customer_name'] . '%');
            });
        }

        if (!empty($param['type'])) {
            $query = $query->where('type', $param['type']);
        }

        if (!empty($status)) {
            $query = $query->whereIn('status_id', $status);
        } else {
            if (!$user->canViewAllOrder()) {
                $query = $query->whereIn('status_id', $user->getViewEnableStatusIds());
            }
        }

        //Nếu là số điện thoại thì ko khóa
        if (!empty($param['customer_phone_or_code']) && strlen(trim($param['customer_phone_or_code'])) >= 10) {
            // Thì cho xem để check xem ô có hack ko :D
            // Log::info(strlen($param['customer_phone_or_code']));
        } else {
            if (!$user->canViewAllOrder()) {
                if ($user->hasPermission('bungdon') || $user->hasPermission('bungdon3')) {

                } elseif ($user->hasPermission('bungdon2')) {
                    //Không làm gì :D
                    $query->whereNotIn('status_id', [COMPLETE_ORDER_STATUS_ID, DELIVERY_ORDER_STATUS_ID]);
                } else {
                    $query = $query->where(function ($subQuery) use ($user) {
                        $subQuery->where('user_created', $user->id)
                            ->orWhere('upsale_from_user_id', $user->id)
                            ->orWhere('assigned_user_id', $user->id)
                            ->orWhere('close_user_id', $user->id)
                            ->orWhere('delivery_user_id', $user->id)
                            ->orWhere('user_created', $user->id)
                            ->orWhere('marketing_id', $user->id);
                    });
                }
            }
        }

        $sort = 'id';
        $order = 'desc';
        if (isset($param['sort'])) {
            $sort = $param['sort'];
            $order = $param['order'];
            if ($param['sort'] == 'status') {
                $sort = 'status_id';
            }
            if ($param['sort'] == 'customer' || $param['sort'] == 'customer.phone' || $param['sort'] == 'customer.address') {
                $sort = 'customer_id';
            }
            if ($param['sort'] == 'source') {
                $sort = 'source_id';
            }
            if ($param['sort'] == 'assigned_user') {
                $sort = 'assigned_user_id';
            }
            if ($param['sort'] == 'close_user.account_id') {
                $sort = 'close_user_id';
            }
            if ($param['sort'] == 'delivery_user.account_id') {
                $sort = 'delivery_user_id';
            }
            if ($param['sort'] == 'user_created_obj.name') {
                $sort = 'user_created';
            }
            if ($param['sort'] == 'upsale_from_user.account_id') {
                $sort = 'upsale_from_user_id';
            }
            if ($param['sort'] == 'shipping_service.name') {
                $sort = 'shipping_service_id';
            }
        }
        return $query->onlyCurrentShop()->orderBy($sort, $order);
    }

    public function getById($id)
    {
        return $this->model::query()->onlyCurrentShop()->with('order_products.product')->whereKey($id)->firstOrFail();
    }

    public function findWithProductCustomer($id)
    {
        return $this->model::query()->onlyCurrentShop()->with(['order_products.product', 'customer'])->whereKey($id)->firstOrFail();
    }

    public function getByIds($ids, $relations = null)
    {
        $query = $this->model::query()->onlyCurrentShop();
        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->whereIn('id', $ids)->get();
    }

    public function create($customerId, $data, $productData, $isOldCustomer = 0)
    {
        $data = $this->getInsertOrUpdateData($customerId, $data, $productData, null);
        //Kiểm tra xem có đơn nào trong 48h ko thì cảnh báo
        $before2Day = Carbon::now()->subDays(2)->format('Y-m-d');
        $duplicateOrderQuery = $this->model::query()->where('phone', $data['phone'])
            ->where('create_date', '>=', $before2Day)->where('shop_id', getCurrentUser()->shop_id);
        if (!empty($order)) {
            $duplicateOrderQuery = $duplicateOrderQuery->where('id', '!=', $order->id);
        }

        $duplicateorder = $duplicateOrderQuery->first();
        if (!empty($duplicateorder)) {
            $data['close_duplicated_order_id'] = $duplicateorder->id;
        }
        //Generate Code
        $code = $this->generateCode();
        $data['code'] = $code;
        $data['user_created'] = getCurrentUser()->id;
        $data['create_user_type'] = User::class;
        $data['create_date'] = Carbon::now();
        $data['shop_id'] = getCurrentUser()->shop_id;
        $existOrder = $this->getDuplicateOrderByPhone($data['phone']);
        if (!empty($existOrder)) {
            $data['duplicated'] = $existOrder->id;
        }

        $order = $this->model::query()->create($data);

        if ($order->status_id == DELIVERY_ORDER_STATUS_ID) {
            $this->decreaseQuantityProduct($order);
        }

        return $order;
    }

    public function update($orderId, $customerId, $data, $productData)
    {
        $order = $this->model::query()->whereKey($orderId)->firstOrFail();
        $oldData = $order->toArray();
        $data = $this->getInsertOrUpdateData($customerId, $data, $productData, $order);
        $order->fill($data);
        $changes = $order->getDirty();
        $order->save();

        $updateData = [];

        foreach ($changes as $field => $value) {
            $updateData[$field] = [
                'old_value' => !empty($oldData[$field]) ? $oldData[$field] : '',
                'new_value' => $value,
            ];
        }

        return $updateData;
    }

    private function getInsertOrUpdateData($customerId, $data, $productData, $order = null)
    {
        $data['customer_id'] = $customerId;
        $data['is_top_priority'] = !empty($data['is_top_priority']) ? 1 : 0;
        $data['is_send_sms'] = !empty($data['is_send_sms']) ? 1 : 0;
        $data['is_inner_city'] = !empty($data['is_inner_city']) ? 1 : 0;

        $price = 0;
        foreach ($productData as $product) {
            $price += convertPriceToInt($product['price']) * intval($product['quantity']);
        }

        //Price
        $discountPrice = convertPriceToInt($data['discount_price']);
        $shippingPrice = convertPriceToInt($data['shipping_price']);
        $otherPrice = convertPriceToInt($data['other_price']);

        $totalPrice = $price - $discountPrice + $shippingPrice + $otherPrice;

        $data['price'] = $price;
        $data['discount_price'] = $discountPrice;
        $data['shipping_price'] = $shippingPrice;
        $data['other_price'] = $otherPrice;
        $data['total_price'] = $totalPrice;

        //Ngày chuyển kế toán
        if (!empty($data['upsale_from_user_id'])) {
            $data['assign_accountant_date'] = Carbon::now();
        }

        //Ngày chia
        if (!empty($data['assigned_user_id']) && (empty($order) || $order->assigned_user_id != $data['assigned_user_id'])) {
            $data['share_date'] = Carbon::now();
            if (!empty($order) && !empty($order->assigned_user_id) && $order->assigned_user_id != $data['assigned_user_id']) {
                $data['read'] = 0;
            }
        }

        $user = getCurrentUser();

        //Kiểm tra trùng đơn
        $productIds = array_column($productData, 'product_id');
        if (!empty($productIds)) {
            $isDuplicatedOrderQuery = DB::table('orders')
                ->join('order_products', function ($join) use ($data) {
                    $join->on('orders.id', '=', 'order_products.order_id')
                        ->whereIn('orders.status_id', STATUS_DON_HANG_THANH_CONG)
                        ->where('orders.phone', $data['phone']);
                })->whereIn('product_id', $productIds)->where('orders.shop_id', $user->shop_id);
            if (!empty($order)) {
                $isDuplicatedOrderQuery->where('orders.id', '!=', $order->id);
            }

            $isDuplicatedOrder = $isDuplicatedOrderQuery->first();
            $data['is_old_customer'] = !empty($isDuplicatedOrder);
        }

        if (empty($order) || (!empty($data['status_id']) && !empty($order->status_id) && $order->status_id != $data['status_id'])) {
            //Ngày chốt
            if ($data['status_id'] == CLOSE_ORDER_STATUS_ID) {
                $data['close_user_id'] = $user->id;
                $data['close_user_type'] = User::class;
                $data['close_date'] = Carbon::now();
            }

            //Ngày chuyển hàng
            if ($data['status_id'] == DELIVERY_ORDER_STATUS_ID) {
                $data['delivery_user_id'] = $user->id;
                $data['delivery_user_type'] = User::class;
                $data['delivery_date'] = Carbon::now();
            }
            //Ngày Thành công
            if ($data['status_id'] == COMPLETE_ORDER_STATUS_ID) {
                $data['complete_date'] = Carbon::now();
            }

            //Trạng thái đơn hàng cũ thì tính doanh thu là đơn cũ
            if ($data['status_id'] == OLD_ORDER_STATUS_ID) {
                $data['is_old_customer'] = 1;
                try {
                    LogIsOldCustomer::create([
                        'order_id' => '',
                        'phone' => $data['phone'],
                        'customer_id' => $customerId,
                        'controller' => 'OrderRepository',
                        'function' => 'getInsertOrUpdateData',
                        'reason' => 'OLD_ORDER_STATUS_ID',
                    ]);
                } catch(\Exception $err) {
                    Log:error($err);
                }

            }
            //Ngày Thu tiền
            if ($data['status_id'] == COLLECT_MONEY_ORDER_STATUS_ID) {
                $data['collect_money_date'] = Carbon::now();
            }

            //Ngày Chuyển hoàn
            if ($data['status_id'] == REFUND_ORDER_STATUS_ID) {
                $data['refund_date'] = Carbon::now();

                // Nếu trạng thái đơn hàng trước đó là chuyển hàng --> cộng lại vào kho
                if (!empty($order) && $order->status_id == DELIVERY_ORDER_STATUS_ID) {
                    $this->increaseQuantityProduct($order);
                }
            }

            //Ngày hủy
            if ($data['status_id'] == CANCEL_ORDER_STATUS_ID) {
                $data['cancel_date'] = Carbon::now();
                $data['cancel_user_id'] = $user->id;
                $data['cancel_user_type'] = User::class;
            }


        }

        return $data;
    }

    public function generateCode()
    {
        $code = 10000;

        $latestOrder = $this->model::query()->withTrashed()->latest('created_at')->first();
        if (!empty($latestOrder)) {
            $code = $latestOrder->code + 1;
        }

        $isExist = $this->model::query()->withTrashed()->where('code', $code)->exists();

        if (!empty($isExist)) {
            return $this->generateCode();
        }

        return $code;
    }

    public function updateStatus($ids, $statusId, $closeStatus, $status)
    {
        $user = getCurrentUser();
        $data = ['status_id' => $statusId];

        $orders = Order::query()->onlyCurrentShop()->whereIn('id', $ids)->get();

        $updateDatas = [];
        foreach ($orders as $order) {
            //Cập nhật thông tin nếu trạng thái thay đổi
            if ($order->status_id != $statusId) {
                //Ngày chốt
                if ($data['status_id'] == CLOSE_ORDER_STATUS_ID) {
                    $data['close_user_id'] = $user->id;
                    $data['close_user_type'] = User::class;
                    $data['close_date'] = Carbon::now();

                    //Kiểm tra xem có đơn nào chốt trong 48h ko thì cảnh báo
                    // $before2Day = Carbon::now()->subDays(2)->format('Y-m-d');
                    // $existCloseDuplicatedOrder = $this->model::query()->where('phone', $order->phone)
                    //                                 ->where('close_date', '>=', $before2Day)->where('id', '!=', $order->id)->where('shop_id', $user->shop_id)->first();
                    // if (!empty($existCloseDuplicatedOrder)) {
                    //     $data['close_duplicated_order_id'] = $existCloseDuplicatedOrder->id;
                    // }
                }
                //Ngày chuyển hàng
                if ($data['status_id'] == DELIVERY_ORDER_STATUS_ID) {
                    $data['delivery_user_id'] = $user->id;
                    $data['delivery_user_type'] = User::class;
                    $data['delivery_date'] = Carbon::now();

                    $this->decreaseQuantityProduct($order);
                }
                //Ngày Thành công
                if ($data['status_id'] == COMPLETE_ORDER_STATUS_ID) {
                    $data['complete_date'] = date('Y-m-d H:i:s');
                    $latest_order = Order::where('id', '<>', $order->id)->where('customer_id', $order->customer_id)->where('bundle_id',$order->bundle_id)->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->count();
                    $data['returned'] = $latest_order + 1;
                }

                //Ngày Thu tiền
                if ($data['status_id'] == COLLECT_MONEY_ORDER_STATUS_ID) {
                    $data['collect_money_date'] = Carbon::now();
                }

                //Ngày Chuyển hoàn
                if ($data['status_id'] == REFUND_ORDER_STATUS_ID) {
                    $data['refund_date'] = Carbon::now();

                    if ($order->status_id == DELIVERY_ORDER_STATUS_ID) {
                        $this->increaseQuantityProduct($order);
                    }
                }

                //Ngày hủy
                if ($data['status_id'] == CANCEL_ORDER_STATUS_ID) {
                    $data['cancel_date'] = Carbon::now();
                    $data['cancel_user_id'] = $user->id;
                    $data['cancel_user_type'] = User::class;
                }

                // Kiểm tra xem có phải đơn hàng cũ không
                /*
                $isOldCustomer = Order::query()->onlyCurrentShop()->where('customer_id', $order->customer_id)->whereHas('status', function ($q) use ($closeStatus) {
                    $q->where('level', '>=', $closeStatus->level);
                })->where('id', '<>', $order->id)->first();
                $order->is_old_customer = empty($isOldCustomer) ? 0 : 1;
                if (!empty($isOldCustomer)) {
                    try {
                        LogIsOldCustomer::create([
                            'order_id' => $order->id,
                            'phone' => $order->phone,
                            'customer_id' => $order->customer_id,
                            'controller' => 'OrderRepository',
                            'function' => 'update status',
                            'reason' => 'update status',
                        ]);
                    } catch (\Exception $err) {
                        Log::error($err);
                    }
                }
                */

                //Kiểm tra trùng đơn
                $orderProducts = $order->order_products;
                $productIds = !empty($orderProducts) ? $orderProducts->pluck('product_id') : null;
                if (!empty($productIds)) {
                    $isDuplicatedOrderQuery = DB::table('orders')
                        ->join('order_products', function ($join) use ($order) {
                            $join->on('orders.id', '=', 'order_products.order_id')
                                ->whereIn('orders.status_id', STATUS_DON_HANG_THANH_CONG)
                                ->where('orders.phone', $order->phone);
                        })->whereIn('product_id', $productIds)->where('orders.shop_id', $user->shop_id);
                    if (!empty($order)) {
                        $isDuplicatedOrderQuery->where('orders.id', '!=', $order->id);
                    }

                    $isDuplicatedOrder = $isDuplicatedOrderQuery->first();
                    $data['is_old_customer'] = !empty($isDuplicatedOrder);
                }

                //Trạng thái đơn hàng cũ thì tính doanh thu là đơn cũ
                if ($data['status_id'] == OLD_ORDER_STATUS_ID) {
                    $data['is_old_customer'] = 1;
                    try {
                        LogIsOldCustomer::create([
                            'order_id' => $order->id,
                            'phone' => $order->phone,
                            'customer_id' => $order->customer_id,
                            'controller' => 'OrderRepository',
                            'function' => 'update status',
                            'reason' => 'OLD_ORDER_STATUS_ID',
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }

                $oldStatusId = $order->status_id;
                $order->fill($data);
                $order->save();

                $updateData['status_id'] = [
                    'old_value' => $oldStatusId,
                    'new_value' => $statusId,
                ];

                $updateDatas[$order->id] = $updateData;
            }
        }
        return $updateDatas;
    }

    public function countNotAssignOrder($param)
    {
        $query = $this->model::query()->whereNull('assigned_user_id');
        if (!empty($param['source_id'])) {
            $query = $query->where('source_id', $param['source_id']);
        }

        if (!empty($param['bundle_id'])) {
            $query = $query->where('bundle_id', $param['bundle_id']);
        }

        return $query->onlyCurrentShop()->count();
    }

    public function getNotAssignOrder($param)
    {
        $query = $this->model::query()->whereNull('assigned_user_id');
        if (!empty($param['source_id'])) {
            $query = $query->where('source_id', $param['source_id']);
        }

        if (!empty($param['bundle_id'])) {
            $query = $query->where('bundle_id', $param['bundle_id']);
        }

        return $query->onlyCurrentShop()->get();
    }

    public function assignOrdersForSale($orderIds, $saleId)
    {
        if ($saleId != -1) {
            $this->model::query()->onlyCurrentShop()->whereIn('id', $orderIds)->update([
                'assigned_user_id' => $saleId,
                'share_date' => Carbon::now(),
                'read' => 0,
            ]);
        } else {
            $this->model::query()->onlyCurrentShop()->whereIn('id', $orderIds)->update([
                'assigned_user_id' => null,
                'share_date' => null,
            ]);
        }
    }

    public function assignOrdersForMarketing($orderIds, $marketingId)
    {
        $this->model::query()->onlyCurrentShop()->whereIn('id', $orderIds)->update([
            'upsale_from_user_id' => $marketingId,
        ]);
    }

    public function assignOrdersForGroup($orderIds, $groupId)
    {
        $group = UserGroup::query()->with('users')->whereKey($groupId)->firstOrFail();
        $users = $group->users;

        $userIndex = 0;
        $countUser = count($users);

        $changed = []; // Những thay đổi để lưu vào lịch sử
        foreach ($orderIds as $index => $orderId) {
            if ($userIndex >= $countUser) {
                $userIndex = 0;
            }

            $user = $users[$userIndex];

            if ($user->isSale()) {
                $this->model::query()->onlyCurrentShop()->whereKey($orderId)->update([
                    'assigned_user_id' => $user->id,
                    'share_date' => Carbon::now(),
                    'read' => 0,
                ]);

                array_push($changed, [
                    'order_id' => $orderId,
                    'user_id' => $user->id,
                    'account_id' => $user->account_id,
                ]);
            }

            $userIndex += 1;
        }

        return $changed;
    }

    public function searchByPhone($phone)
    {
        return $this->model::query()->onlyCurrentShop()->with(['customer', 'user_created_obj', 'status', 'bundle', 'province', 'district', 'ward'])->whereHas('customer', function ($q) use ($phone) {
            $q->where('phone', $phone);
        })->onlyCurrentShop()->get();
    }

    public function getFilterStatus()
    {
        $user = getCurrentUser();
        $query = OrderStatus::query();
        if (!$user->isAdmin()) {
            $enableStatusIds = $user->getViewEnableStatusIds();
            $query = $query->whereIn('id', $enableStatusIds);
        }
        return $query->currentShop()->orderBy('level')->get();
    }

    //get data tỷ lệ chốt đơn
    public function getReportMarketingRevenue($params)
    {
        $query = array();
        if (isset($params['status_ids']) && !empty($params['status_ids'])) {
            foreach ($params['status_ids'] as $item) {
                $q = 'COUNT(IF(orders.status_id = ' . $item . ',1,NULL)) count_' . $item . ',';
                $q .= 'SUM(IF(orders.status_id = ' . $item . ',orders.total_price,0)) sum_' . $item . '';
                array_push($query, $q);
            }
        }
        $data = User::leftJoin('orders', function ($join) use ($params) {
            $type_date = isset($params['type_date']) ? $params['type_date'] : 'close_date';
            $join->whereRaw('(users.id = orders.marketing_id OR users.id = orders.upsale_from_user_id OR users.id = orders.user_created)')
                ->whereIn('orders.status_id', $params['status_ids'])
                ->where('orders.shop_id', getCurrentUser()->shop_id)->where('users.shop_id', getCurrentUser()->shop_id);

            if (isset($params['user_group_id']) && $params['user_group_id'] != null) {
                $join->where('users.user_group_id', $params['user_group_id']);
            }
            if (isset($params['status']) && $params['status'] != null) {
                $join->where('users.status', $params['status']);
            }
            if (isset($params['order_type']) && $params['order_type'] != null) {
                $join->where('orders.type', $params['order_type']);
            }
            if (!empty($params['from'])) {
                $join->where(function ($q) use ($params,$type_date) {
                    $q->where('orders.'.$type_date, '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
                    // ->orWhere('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
                });
            }
            if (!empty($params['to'])) {
                $join->where(function ($q) use ($params,$type_date) {
                    $q->where('orders.'.$type_date, '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
                    // ->orWhere('orders.share_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
                });
            }
        })
            // ->where('users.status', ACTIVE)
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->whereNull('orders.deleted_at');

        $string = implode($query, ',');
        $data = $data->select('users.id', 'users.name as user_name', 'users.account_id', DB::raw('SUM(orders.total_price) sum_total'), DB::raw('COUNT(orders.status_id) count_total'))
            ->selectRaw($string)
            ->groupBy('users.id', 'users.name')->get();
        return $data;
    }
    public function mktSourceQuery($params)
    {
        $query = Order::join('order_sources', 'order_sources.id', 'orders.source_id')
        // ->join('shops', 'shops.id', 'orders.shop_id')
        // ->leftjoin('customers', 'customers.id', 'orders.customer_id')
        // ->where('orders.assigned_user_id', '!=', 'shops.owner_id')
        // ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT)
            ->whereNull('orders.deleted_at')
        // ->whereNull('orders.duplicated')
            ->where('orders.shop_id', getCurrentUser()->shop_id);
        if (!empty($params['marketing_id'])) {
            $query = $query->where(function ($query) use ($params) {
                return $query->where('orders.upsale_from_user_id', $params['marketing_id'])
                    ->orWhere('orders.marketing_id', $params['marketing_id'])
                    ->orWhere('orders.user_created', $params['marketing_id']);
            });
        } else {
            // if (isset($params['active_account']) && ($params['active_account'] == 1||$params['active_account'] == 2)) {
            //     $query = $query->where(function ($query) use ($params) {
            //         $ids = $params['mkt_ids'];
            //         return $query->where('orders.upsale_from_user_id', $ids)
            //             ->orWhere('orders.marketing_id', $ids)
            //             ->orWhere('orders.user_created', $ids);
            //     });
            // }
        }
        $fieldView = isset($params['view_id']) && ($params['view_id'] == "close_date" || $params['view_id'] == "assign_accountant_date") ? $params['view_id'] : "created_at";
        if (!empty($params['from'])) {
            $query = $query->whereDate('orders.' . $fieldView, '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (!empty($params['to'])) {
            $query = $query->whereDate('orders.' . $fieldView, '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
        return $query;
    }
    //get mkt theo nguồn
    public function getMktSource($params, $sources)
    {
        $query = $this->mktSourceQuery($params);
        $total = $this->getTotalMktSource($params);

        $data = $query->select(
            'order_sources.name as source_name',
            'order_sources.id as source_id',
            DB::raw('Count(orders.id) as count_phone'),
            DB::raw('Count(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),1,NULL)) as count_order'),
            DB::raw('SUM(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),orders.total_price,NULL)) as sum_price'),
            DB::raw("COUNT(IF(orders.status_id IN (" . implode(", ", STATUS_DON_HANG_CHOT) . "),1,NULL)) / COUNT(orders.id) * 100 as percent")
        )
            ->groupBy('orders.source_id', 'order_sources.name')
            ->get();

        if ($data != null) {
            $data = $data->toArray();
        }
        $result = $this->mapSource($data, $sources);
        $result['total'] = $total;
        return $result;

    }

    //Biểu đồ tỷ lệ chốt của MKT
    public function bieuDoTyLeChotMkt($mkts, $params)
    {
        $fromOfDay = Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay();
        $toOfDay = Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay();
        $chartData = array();
        if (!empty($params['marketing_id']) && $fromOfDay < $toOfDay) {
            $data = Order::where('orders.shop_id', auth()->user()->shop_id)
                ->join('users', function ($join) {
                    $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                        ->whereNull('users.deleted_at');
                })
                ->whereBetween('orders.created_at', [$fromOfDay, $toOfDay]);
            $data->select('users.name as user_name', 'users.id as user_id', 'users.account_id',
                DB::raw('DATE_FORMAT(orders.created_at,"%Y/%m/%d") month_day'),
                DB::raw('COUNT(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),1,NULL)) / COUNT(orders.id) * 100 ty_le_chot'));
            $data = $data->where('users.id', $params['marketing_id'])
                ->groupBy('users.account_id', "month_day")
                ->get();

            foreach ($data as $mkt) {
                $chartData[$mkt->month_day] = floatval($mkt->ty_le_chot);
            }
            for ($date = $fromOfDay->copy(); $date->lte($toOfDay); $date->addDay()) {
                $id = $date->format('Y/m/d');
                if (!isset($chartData[$id])) {
                    $chartData[$id] = 0;
                }
            }
            ksort($chartData);
            return $chartData;
        }
        $data = Order::where('orders.shop_id', auth()->user()->shop_id)
            ->join('users', function ($join) {
                $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                    ->whereNull('users.deleted_at');
            })
            ->whereBetween('orders.created_at', [$fromOfDay, $toOfDay]);

        $data->select('users.name as user_name', 'users.id as user_id', 'users.account_id',
            DB::raw('COUNT(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),1,NULL)) / COUNT(orders.id) * 100 ty_le_chot'));
        $data = $data->whereIn('users.id', array_keys($mkts))
            ->groupBy('users.account_id')
            ->get();
        foreach ($data as $mkt) {
            $chartData[$mkt->account_id] = floatval($mkt->ty_le_chot);
        }
        return $chartData;
    }

    //get tổng mkt theo nguồn
    public function getTotalMktSource($params)
    {
        $query = $this->mktSourceQuery($params);
        $data = $query->select(
            DB::raw('Count(orders.phone) as count_phone'),
            DB::raw('Count(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),1,NULL)) as count_order'),
            DB::raw('SUM(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),orders.total_price,NULL)) as sum_price')
            // DB::raw("1 / 1 * 100 as percent")
        )
            ->first();
        if ($data != null) {
            $data = $data->toArray();
        }
//        dd($data);
        return $data;

    }

    public function mapSource($data, $sources)
    {
        $table = array();
        $result = array();
        $chart = array();
        if (!empty($data) > 0 && !empty($sources)) {
            $source_ids = array_keys($sources);
            $selected = array();
            foreach ($data as $item) {
                if (in_array($item['source_id'], $source_ids)) {

                    array_push($table, $item);
                    array_push($selected, $item['source_id']);
                    $chart[$item['source_name']] = $item['sum_price'];
                }
            }
            foreach ($sources as $key => $source) {
                if (!in_array($key, $selected)) {
                    $item = [
                        'source_name' => $source,
                        'source_id' => $key,
                        'count_phone' => 0,
                        'count_order' => 0,
                        'sum_price' => 0,
                        'percent' => 0,
                    ];
                    array_push($table, $item);
                    $chart[$source] = 0;
                }
            }
        }

        $result['table'] = $table;
        $result['chart'] = $chart;
        return $result;
    }

    public function getOrderStatus($params, $status)
    {
        $result = array();
        if (empty($status)) {
            return $result;
        }
        $shop_owner_id = Shop::where('id', getCurrentUser()->shop_id)->pluck('owner_id')->first();
        $query = Order::leftJoin('order_status', 'order_status.id', 'orders.status_id')
            ->whereMonth('orders.close_date', $params['month'])
            ->whereYear('orders.close_date', $params['year'])
        // ->whereNull('orders.duplicated')
            ->where('orders.shop_id', getCurrentUser()->shop_id);
        if (isset($params['status']) && !empty($params['status'])) {
            $query = $query->whereIn('orders.status_id', $params['status']);
        }
        if ($shop_owner_id != null) {
            $query = $query->where(function ($q) use ($shop_owner_id) {
                $q->where('orders.user_created', '!=', $shop_owner_id)
                    ->orWhere('orders.assigned_user_id', '!=', $shop_owner_id);
            });
        }
        $query = $query->select('order_status.name', 'orders.status_id',
            DB::raw('Count(orders.id) as count_order')
        )
            ->groupBy('orders.status_id', 'order_status.name')->get();

        $default_data = $this->getDefaultData($status);
        foreach ($query as $value) {
            if (isset($default_data[$value->status_id])) {

                $default_data[$value->status_id] = $this->itemPieChar($value->name, $value->count_order, true, true);
            }
        }
        return $default_data;
    }

    public function getDefaultData($status)
    {
        $data = array();
        foreach ($status as $id => $name) {
            $data[$id] = $this->itemPieChar($name, 0, true, true);
        }
        return $data;
    }

    public function itemPieChar($name, $y, $sliced, $selected)
    {
        $data = [
            'name' => $name,
            'y' => $y,
            'sliced' => $sliced,
            'selected' => $selected,
        ];
        return $data;
    }

    public function getProvinceChart($params)
    {
        $provinces = $params['provinces'];
        $query = Order::join('order_status', 'order_status.id', 'orders.status_id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->join('province', 'province.id', 'orders.province_id')
            ->where('orders.assigned_user_id', '!=', 'shops.owner_id')
            ->whereMonth('orders.close_date', $params['month'])
            ->whereYear('orders.close_date', $params['year'])
            ->where('orders.shop_id', getCurrentUser()->shop_id);

        if (isset($params['status']) && $params['status'] != null) {
            $query = $query->whereIn('order_status.id', $params['status']);
        }

        $query = $query->select('province._name as name', 'orders.province_id',
            DB::raw('Count(orders.id) as count_order')
        )
            ->groupBy('orders.province_id', 'province._name')
            ->get();
        $default_data = $this->getDefaultData($provinces);
        foreach ($query as $value) {
            if (isset($default_data[$value->province_id])) {
                $default_data[$value->province_id] = $this->itemPieChar($value->name, $value->count_order, true, true);
            } else {
                $item = $this->itemPieChar($value->name, $value->count_order, true, true);
                array_push($default_data, $item);
            }
        }
        return $default_data;
    }

    public function getSaleDelivery($params)
    {
        $query = Order::join('order_status', 'order_status.id', 'orders.status_id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->join('users', 'users.id', 'orders.assigned_user_id')
//            ->where('orders.assigned_user_id', '!=', 'shops.owner_id')
        // ->whereNull('orders.duplicated')
            ->where('orders.shop_id', getCurrentUser()->shop_id);
        if (isset($params['from']) && $params['from'] != null) {
            $query = $query->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->format('Y-m-d'));
        }
        if (isset($params['to']) && $params['to'] != null) {
            $query = $query->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->format('Y-m-d'));
        }
        if (isset($params['delivery_id']) && $params['delivery_id'] != null) {
            $query = $query->where('orders.shipping_service_id', $params['delivery_id']);
        }
        if (isset($params['status']) && $params['status'] != null) {
            $query = $query->whereIn('orders.status_id', $params['status']);
        }
//        $data = $query->select('orders.*','users.name')->get();
        $data = $query->select(
            'users.name',
            DB::raw("Count(IF(orders.status_id = " . COMPLETE_ORDER_STATUS_ID . ",1,null)) as count_complete"),
            DB::raw("Sum(IF(orders.status_id = " . COMPLETE_ORDER_STATUS_ID . ",orders.total_price,0)) as sum_complete"),
            DB::raw("Count(IF(orders.status_id = " . REFUND_ORDER_STATUS_ID . ",1,null)) as count_refund"),
            DB::raw("Sum(IF(orders.status_id = " . REFUND_ORDER_STATUS_ID . ",orders.total_price,0)) as sum_refund"),
            DB::raw("Count(IF(orders.status_id = " . DELIVERY_ORDER_STATUS_ID . ",1,null)) as count_delivery"),
            DB::raw("Sum(IF(orders.status_id = " . DELIVERY_ORDER_STATUS_ID . ",orders.total_price,0)) as sum_delivery")
        )
            ->groupBy('users.name', 'users.account_id')
            ->get();
        return $data;
    }

    public function getProductRevenue($params)
    {

    }

    public function getDuplicateOrderByPhone($phone)
    {
        /*
        $closeStatus = OrderStatus::query()->whereKey(CLOSE_ORDER_STATUS_ID)->first();
        return $this->model::query()->onlyCurrentShop()->where('phone', $phone)->whereHas('status', function ($q) use ($closeStatus) {
        $q->where('level', '<', $closeStatus->level);
        })->first();
         */
        return $this->model::query()->onlyCurrentShop()->where('phone', $phone)->first();
    }

    public function getMktRevenue($params)
    {
        $query = Order::where('orders.upsale_from_user_id', $params['marketing_id'])
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT);
        // ->whereNull('orders.duplicated')
        if (!empty($params['from'])) {
            $query = $query->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (!empty($params['to'])) {
            $query = $query->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
        $data = $query->select(
            DB::raw('Sum(IF(orders.price,orders.price,0)) as sum_order'),
            DB::raw('Count(orders.id) as count_order')
        )
            ->groupBy('orders.upsale_from_user_id')
            ->first();
        if ($data != null) {
            $data = $data->toArray();
        }
        return $data;
    }

    //tính % mkt theo từng sale
    public function getMktPercentBySale($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->select(
                DB::raw("SUM(IF(orders.status_id IN (" . implode(",", STATUS_DON_HANG_CHOT) . "),orders.total_price,NULL)) / 1000000 as sum_order")
            )
            ->where(function ($q) use ($params) {
                $q->where('upsale_from_user_id', $params['upsale_from_user_id'])
                    ->orWhere('user_created', $params['upsale_from_user_id']);
            })
            ->where(function ($q) use ($params) {
                $q->where('assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });

        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        //kiểm tra cộng tổng
        $data = $query->first();
        $result = [
            'sum_order' => $data->sum_order,
        ];
        return $result;
    }

    //tính tổng đơn của sale theo ngày chia
    public function getSaleRevenue($params)
    {
        $query = $this->model::select(DB::raw('count(orders.id) as total'))->onlyCurrentShop()
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('orders.user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });
        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        return $query->first()->total;
    }

    //tính doanh thu hotline theo từng sale
    public function getHotlineMoney($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->whereNull('orders.upsale_from_user_id')
            ->where(function ($q) {
                $q->whereNull('is_old_customer')
                    ->orWhere('is_old_customer', '<>', ACTIVE);
            })
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('orders.user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });

        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        $data = $query->select(
            DB::raw("SUM(IF(orders.status_id IN (4,5,7,10),orders.total_price,0)) / 1000000 as sum_order")
        )->first();
        return $data;
    }
    // tính tiền khách hàng cũ đã mua theo từng sale
    public function getOldCustomerMoney($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->where('orders.is_old_customer', '=', ACTIVE)
            ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('orders.user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });

        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        $data = $query->select(
            DB::raw("Sum(orders.total_price) / 1000000 as sum_order")
        )->first();
        return $data;
    }

    //doanh thu khách cũ theo từng mkt
    public function getOldCustomerByMkt($marketers, $params)
    {
        $item = [
            "sum_order" => 0,
            "count_order" => 0,
        ];
        if (empty($marketers)) {
            $result[0] = $item;
        }
        $marketer_ids = array_keys($marketers);

        foreach ($marketer_ids as $id) {
            $query = Order::onlyCurrentShop()
                ->where('orders.is_old_customer', '=', ACTIVE);
            if (!empty($params['from'])) {
                $query = $query->where(function ($q) use ($params) {
                    $q->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
                });
            }
            if (!empty($params['to'])) {
                $query = $query->where(function ($q) use ($params) {
                    $q->where('orders.close_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
                });
            }
            $query->where(function ($q) use ($id) {
                $q->where('orders.upsale_from_user_id', $id)
                    ->orWhere('orders.user_created', $id);
            })
                ->select(
                    DB::raw("SUM(IF(orders.status_id IN (" . implode(",", STATUS_DON_HANG_CHOT) . "),orders.total_price,NULL)) / 1000000 as sum_order")
                );
            $result[$id] = $query->first();
        }
        return $result;
    }

    //tính % mkt theo từng sale
    public function getMktPercentBySalePercent($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->select(
                DB::raw("COUNT(IF(orders.status_id IN (" . implode(",", STATUS_DON_HANG_CHOT) . "),1,NULL)) / count(orders.id) * 100 as percent")
            )
            ->where(function ($q) use ($params) {
                $q->where('upsale_from_user_id', $params['upsale_from_user_id'])
                    ->orWhere('user_created', $params['upsale_from_user_id']);
            })
            ->where(function ($q) use ($params) {
                $q->where('assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });
        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        //kiểm tra cộng tổng
        $data = $query->first();
        $result = [
            'percent' => $data->percent,
        ];
        return $result;
    }

    //tính % hotline theo từng sale
    public function getHotlineMoneyPercent($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->whereNull('orders.upsale_from_user_id')
            ->where(function ($q) {
                $q->whereNull('is_old_customer')
                    ->orWhere('is_old_customer', '<>', ACTIVE);
            })
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('orders.user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });

        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        $data = $query->select(
            DB::raw("COUNT(IF(orders.status_id IN (" . implode(",", STATUS_DON_HANG_CHOT) . "),1,NULL)) / count(orders.id) * 100 as percent")
        )->first();
        return $data;
    }
    // % khách hàng cũ đã mua theo từng sale
    public function getOldCustomerMoneyPercent($params)
    {
        $query = $this->model::query()->onlyCurrentShop()
            ->where('orders.is_old_customer', '=', ACTIVE)
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id'])
                    ->orWhere('orders.user_created', $params['assigned_user_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            });
        }
        if (!empty($params['to'])) {
            $query = $query->where(function ($q) use ($params) {
                $q->where('orders.share_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            });
        }
        $data = $query->select(
            DB::raw("COUNT(IF(orders.status_id IN (" . implode(",", STATUS_DON_HANG_CHOT) . "),1,NULL)) / " . $params['total_order'] . " * 100 as percent")
        )->first();
        return $data;
    }

    public function updateOrder($data, $conditions)
    {
        $this->model::query()->onlyCurrentShop()->where($conditions)->update($data);
    }

    public function decreaseQuantityProduct($order) {
        $orderProducts = $order->order_products;
        if (empty($orderProducts)) { return; }
        foreach ($orderProducts as $orderProduct) {
            StockProduct::query()->where('product_id', $orderProduct->product_id)->where('stock_group_id', KHO_TONG_ID)->decrement('quantity', $orderProduct->quantity);
        }
    }

    public function increaseQuantityProduct($order) {
        $orderProducts = $order->order_products;
        if (empty($orderProducts)) { return; }
        foreach ($orderProducts as $orderProduct) {
            StockProduct::query()->where('product_id', $orderProduct->product_id)->where('stock_group_id', KHO_TONG_ID)->increment('quantity', $orderProduct->quantity);
        }
    }
}
