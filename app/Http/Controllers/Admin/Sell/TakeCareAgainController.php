<?php

namespace App\Http\Controllers\Admin\Sell;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\Admin\CustomerRepository;
use App\Repositories\Admin\OrderFirebaseRepository;
use App\Repositories\Admin\Product\ProductBundleRepository;
use App\Repositories\Admin\Profile\UserRepository;
use App\Repositories\Admin\Sell\DeliveryMethodRepository;
use App\Repositories\Admin\Sell\OrderHistoryRepository;
use App\Repositories\Admin\Sell\OrderProductRepository;
use App\Repositories\Admin\Sell\OrderRepository;
use App\Repositories\Admin\Sell\OrderSourceRepository;
use App\Repositories\Admin\Warehouse\StockGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TakeCareAgainController extends Controller
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

    /**
     * TakeCareAgainController constructor.
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
        OrderFirebaseRepository $orderFirebaseRepository) {
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
        $user = getCurrentUser();
        $data = OrderProduct::query()->leftJoin('products', function ($join) {
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
        // ->where('order_products.called', 0)
        ->where('orders.shop_id', getCurrentUser()->shop_id)
        ->select('order_products.id as id', 'orders.code as code', 
                 'products.name as product_name', 'order_products.quantity as product_quantity',
                 'customers.name as customer_name', 'customers.phone as customer_phone', 'customers.phone2 as customer_phone2',
                 'customers.address as address', 'orders.complete_date as complete_date', 'orders.total_price as total_price',
                 'order_products.called as called')
        ->orderBy('orders.updated_at', 'DESC')
            ->paginate(10);
        return view(VIEW_ADMIN_SELL_ORDER . 'take_care_again', compact('data'));
    }

    public function updateCalled(Request $request, $orderProductId)
    {
        try {
            OrderProduct::query()->whereKey($orderProductId)->update(['called' => 1]);
            return $this->statusOK();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->statusNG();
        }
    }
}