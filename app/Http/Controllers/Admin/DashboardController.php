<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $from = request()->get('from');
        $from = $from != null ? Carbon::createFromFormat('d/m/Y', $from)->format('Y-m-d 00:00:00') : $now->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->get('to');
        $to = $to != null ? Carbon::createFromFormat('d/m/Y', $to)->format('Y-m-d 23:59:59') : Carbon::now()->format('Y-m-d 23:59:59');
        $group_date = [$from, $to];
        $params = [
            'group_date' => $group_date,
        ];
        $user_count = User::onlyCurrentShop()->where('status', ACTIVE)
            ->count();
        $sale_report = $this->getSaleReport($params);
        $data = $this->getData($params);
        $chart_year = $this->getSaleByYear($params);
        $chart_date = $this->getSaleByDate($params);
        $product_report = $this->getProductReport($params);

        return view('admin.dashboard.index', compact('user_count', 'group_date', 'data', 'chart_year', 'chart_date', 'product_report', 'sale_report'));
    }
    public function getData($params)
    {
        $group_date = $params['group_date'];
        $orders = Order::query()->onlyCurrentShop()->whereBetween('created_at', $group_date);
        if (getCurrentUser()->isOnlySale() == true) {
            $orders = $orders->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $orders = $orders->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $orders = $orders->get();
        $transporting = 0;
        $confirmed = 0;
        $divided = 0;
        $no_process = 0;
        $sales = 0;
        $deducted = 0;
        $percent = 0;
        $level2 = 0;
        $donchot = 0;
        $total_order = count($orders);

        if ($total_order > 0) {
            foreach ($orders as $key => $item) {
                //Đơn đang vận chuyển
                if ($item->status_id == DELIVERY_ORDER_STATUS_ID) {
                    $transporting += 1;
                }
                //Đơn xác nhận
                if ($item->status_id == CLOSE_ORDER_STATUS_ID) {
                    $confirmed += 1;
                }
                //Đơn được chia
                if ($item->assigned_user_id != null) {
                    $divided += 1;
                }
                //Đơn chưa xử lý
                if ($item->status_id == NO_PROCESS_ORDER_STATUS_ID) {
                    $no_process += 1;
                }
                //Doanh số
                if (in_array($item->status_id, STATUS_DON_HANG_CHOT)) {
                    $sales += $this->calculatorPrice($item->total_price);
                }
                //Trừ hoàn
                if (in_array($item->status_id, [REFUND_ORDER_STATUS_ID, RETURNED_STOCK_STATUS_ID])) {
                    $deducted += $this->calculatorPrice($item->total_price);
                }
                //Đơn Level > 2
                if ($item->status_id > NO_PROCESS_ORDER_STATUS_ID) {
                    $level2 += 1;
                }
                // Đơn chốt
                if (in_array($item->status_id, STATUS_DON_HANG_CHOT)) {
                    $donchot += 1;
                }

            }
        }

        //Tỉ lệ chốt
        if ($level2 > 0) {
            $percent = ($donchot / $total_order) * 100;
            $percent = round($percent);
        }
        $data = [
            'transporting' => $transporting,
            'confirmed' => $confirmed,
            'divided' => $divided,
            'no_process' => $no_process,
            'sales' => $sales,
            'deducted' => $deducted,
            'percent' => $percent,
        ];
        return $data;
    }
    public function calculatorPrice($price)
    {

        $money = 0;
        if ($price > 0) {
            //chuyển sang triệu
            $money = $price / 1000000;
        }
        return $money;
    }
    public function getSaleByYear($params = false)
    {
        $year = Carbon::now()->format('Y');
        $result = array();
        $data = Order::select(
            DB::raw('sum(total_price)  as total'),
            DB::raw("DATE_FORMAT(orders.close_date,'%c') as month")
        );
        if (isset($params['status'])) {
            $data = $data->whereIN('orders.status_id', $params['status']);
        };
        if (getCurrentUser()->isOnlySale() == true) {
            $data = $data->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $data = $data->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $data = $data->whereYear('orders.close_date', $year)
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->groupBy('month')
            ->orderBy('total', 'desc')
            ->get();
        if (count($data) > 0) {
            $data = $data->pluck('total', 'month')->toArray();
        }
        for ($month = 1; $month <= 12; $month++) {
            $item = isset($data[$month]) ? $data[$month] : 0;
            $result[$month] = $item;
        }
        arsort($result);
        $month_data = [
            'categories' => array_keys($result),
            'data' => array_values($result),
            'data_origin' => $result,
        ];
        return $month_data;
    }
    public function getSaleByDate($params)
    {
        $result = array();
        $data = Order::select(
            DB::raw('sum(orders.total_price)  as total'),
            DB::raw('MONTH(orders.close_date) month'),
            DB::raw('DATE_FORMAT(orders.close_date,"%d-%m") day')
        )
            ->whereBetween('orders.close_date', $params['group_date'])
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT);

        if (getCurrentUser()->isOnlySale() == true) {
            $data = $data->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $data = $data->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $data = $data->where('orders.shop_id', getCurrentUser()->shop_id)
            ->groupBy("month", "day")
            ->orderBy('day')
            ->get();
        if (count($data) > 0) {
            $data = $data->pluck('total', 'day')->toArray();
        }
        $rank_date = $this->dateRange($params['group_date']);
        foreach ($rank_date as $key => $item) {

            $value = isset($data[$item]) ? $data[$item] : 0;
            $result[$item] = $value;
        }
        $date_data = [
            'categories' => array_keys($result),
            'data' => array_values($result),
            'data_origin' => $result,
        ];

        return $date_data;
    }
    public function getProductReport($params)
    {
        $data = array();
        $top_3_money = 0;

        //3 sp có doanh thu cao nhất
        $data_db = OrderProduct::select(
            DB::raw('sum(orders.total_price) as total'),
            'order_products.product_id', 'products.name'
        )
            ->join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id');
        if (getCurrentUser()->isOnlySale() == true) {
            $data_db = $data_db->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $data_db = $data_db->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $data_db = $data_db->whereBetween('orders.close_date', $params['group_date'])
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->groupBy('product_id')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get();

        //tổng sản phẩm
        $total_money = OrderProduct::select(
            DB::raw('sum(orders.total_price) / 1000000  as total')
        )
            ->join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id');
        if (getCurrentUser()->isOnlySale() == true) {
            $total_money = $total_money->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $total_money = $total_money->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $total_money = $total_money->whereBetween('orders.close_date', $params['group_date'])
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->orderBy('total', 'desc')
            ->first('total');
        //xử lý data
        if (count($data_db) > 0) {
            foreach ($data_db as $key => $item) {
                $total = $this->calculatorPrice($item->total);
                $name = $item->name . ' - ' . number_format($total, 2) . 'tr';
                $top_3_money += $total;
                $result = [
                    'name' => $name,
                    'y' => $total,
                    'sliced' => 'true',
                    'selected' => 'true',
                ];
                array_push($data, $result);
            }
        }
        $other_money = $total_money->total - $top_3_money;
        $other = [
            'name' => 'Khác' . ' - ' . number_format($other_money, 2) . 'tr',
            'y' => $other_money,
            'sliced' => 'true',
            'selected' => 'true',
        ];
        array_push($data, $other);
        return $data;
    }
    public function getSaleReport($params)
    {
        $data = array();
        $categories = array();
        $amount = array();
        //3 nv có doanh thu cao nhất
        $data_db = Order::leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'orders.assigned_user_id');
            $join->orOn('users.id', '=', "orders.user_created");
            $join->orOn('users.id', '=', "orders.marketing_id");
            $join->orOn('users.id', '=', "orders.upsale_from_user_id");
        })
            ->leftJoin('order_status', 'order_status.id', 'orders.status_id');
        if (isset($params['status'])) {
            $data_db = $data_db->whereIN('orders.status_id', $params['status']);
        }
        if (getCurrentUser()->isOnlySale() == true) {
            $data_db = $data_db->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.assigned_user_id', getCurrentUser()->id);
            });
        }
        if (getCurrentUser()->isOnlyMarketing() == true) {
            $data_db = $data_db->where(function ($q) {
                $q->where('orders.user_created', getCurrentUser()->id)
                    ->orWhere('orders.upsale_from_user_id', getCurrentUser()->id);
            });
        }
        $data_db = $data_db->whereBetween('orders.close_date', $params['group_date'])
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->select(
                DB::raw('sum(orders.total_price)  as total'),
                DB::raw('count(orders.id)  as count_amount'),
                DB::raw('count(IF(orders.status_id = ' . CANCEL_ORDER_STATUS_ID . ',1,NULL)) count_cancel'),
                'users.name'
            )
            ->groupBy('users.id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();
        //xử lý data
        if (count($data_db) > 0) {
            foreach ($data_db as $item) {
                array_push($categories, $item->name);
                array_push($data, $item->total);
                array_push($amount, $item->count_amount);
            }
        }
        $result = [
            'categories' => $categories,
            'data' => $data,
            'amount' => $amount,
        ];
        return $result;
    }
    // nhập vào mảng [ngày bắt đầu, ngày kết thúc ] => mảng danh sách ngày trong khoảng
    public function dateRange($date, $format = false, $step = '+1 day')
    {
        if ($format == false) {
            $format = 'd-m';
        }
        $dates = array();
        $first = $date[0];
        $last = $date[1];
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }
}