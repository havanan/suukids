<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\Admin\Profile\UserRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportProcessingOrderController extends Controller
{
    //
    public function orderUnprocessed(Request $request)
    {
        $userLogin = Auth::user();
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
        ];
        $data = $request->all();
        if (isset($data['create_date_from'])) {
            $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->startOfDay();
        }
        if (isset($data['create_date_to'])) {
            $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->endOfDay();
        }

        $builder = Order::query();
        $builder = $builder->select('orders.code', 'order_status.name as status_name', 'customers.name', 'customers.phone', 'order_sources.name as sources_name',
            'user_create.name as user_create_name', 'orders.created_at', 'user_share.name as user_shared_name', 'orders.share_date')
            ->leftJoin('customers', 'customers.id', 'orders.customer_id')
            ->leftJoin('order_sources', 'order_sources.id', 'orders.source_id')
            ->leftJoin('users as user_create', 'user_create.id', 'orders.user_created')
            ->leftJoin('users as user_share', 'user_share.id', 'orders.assigned_user_id')
            ->leftJoin('order_status', 'order_status.id', 'orders.status_id')
            ->where('orders.status_id', UNCONFIMRED)
            ->where('orders.shop_id', $userLogin->shop_id)
            ->whereNull('orders.deleted_at')
            ->whereBetween('orders.created_at', [$conditions['create_date_from'], $conditions['create_date_to']]);
        $listOrder = $builder->paginate(10);
        // dd($listOrder);
        return view('admin.report.order_unprocessed', compact('conditions', 'userLogin', 'listOrder'));
    }

    public function employeeOfOrder(Request $request)
    {
        $userLogin = Auth::user();
        $ownerShop = Shop::findOrFail($userLogin->shop_id);
        $userList = User::where('shop_id', $userLogin->shop_id)->where('id', '!=', $ownerShop->owner_id)->paginate(10);
        $orderStatus = OrderStatus::pluck('name', 'id')->toArray();
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
        ];
        $data = $request->all();
        if (isset($data['create_date_from'])) {
            $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->startOfDay();
        }
        if (isset($data['create_date_to'])) {
            $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->endOfDay();
        }
        $order = new Order();
        // $conditions['orderStatus'] = $orderStatus;
        // dd($orderStatus);
        return view('admin.report.employee_order', compact('conditions', 'userLogin', 'userList', 'orderStatus', 'order'));
    }

    public function warehouseSaleNumber(Request $request, UserRepository $userRepository)
    {
        $userLogin = Auth::user();
        $orderType = OrderType::pluck('name', 'id')->toArray();
        $ownerShop = Shop::find($userLogin->shop_id);
        $sales = $userRepository->getArrSales();
        if (isset($sales[$ownerShop->owner_id])) {
            unset($sales[$ownerShop->owner_id]);
        }

        $usersList = User::whereIn('id', array_keys($sales))->active()->get();
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
        ];

        if ($request->isMethod('post')) {
            $conditions = $request->all();
            if (isset($conditions['create_date_from'])) {
                $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $conditions['create_date_from'])->startOfDay();
            }
            if (isset($conditions['create_date_to'])) {
                $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $conditions['create_date_to'])->endOfDay();
            }
        }
        $conditions['sales'] = $sales;
        $data = $userRepository->getReportWarehouseSaleNumber($conditions);
        return view('admin.report.warehouse_sale_number', compact('conditions', 'userLogin', 'orderType', 'usersList', 'data'));
    }
    //doanh thu theo ngay

    public function dailyTurnover(Request $request)
    {
        $userLogin = Auth::user();
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
        ];
        $data = $request->all();
        if (isset($data['create_date_from'])) {
            $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->startOfDay();
        }
        if (isset($data['create_date_to'])) {
            $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->endOfDay();
        }
        $listOrder = $query = Order::select('customers.name', 'customers.phone', 'orders.*')
            ->join('customers', 'orders.customer_id', 'customers.id')
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->whereNull('orders.deleted_at')
            ->whereBetween('orders.created_at', [$conditions['create_date_from'], $conditions['create_date_to']])->paginate(10);

        // dd($listOrder);
        return view('admin.report.daily_turnover', compact('conditions', 'userLogin', 'listOrder'));
    }
}
