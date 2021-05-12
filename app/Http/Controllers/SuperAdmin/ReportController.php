<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function shop(Request $request)
    {
        if (!empty($request->get('start_date'))) {
            $from = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        } else {
            $from = Carbon::createFromFormat('d/m/Y', date('01/m/Y'));
        }

        if (!empty($request['end_date'])) {
            $to = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));
        } else {
            $to = Carbon::now();
        }

        $dataShop = Order::select(
            DB::raw('SUM(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),orders.total_price,NULL)) as total_price'),
            'shops.id as shop_id',
            'shops.name as shop_name'
        )
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->whereBetween('orders.close_date', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('shop_id')
            ->get();
        $collection = collect($dataShop)->sortByDesc('total_price');
        if (isset($request['sort'])) {
            if ($request['direction'] == 'asc') {
                $collection = $collection->sortBy($request['sort']);
            }
            if ($request['direction'] == 'desc') {
                $collection = $collection->sortByDesc($request['sort']);
            }
        }

        $perPage = 20;
        $totalDataCollection = count($collection);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $collection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $shopPaginate = new LengthAwarePaginator($currentPageItems, $totalDataCollection, $perPage);
        $url = url()->current();
        $shopPaginate->setPath($url);
        return view('superadmin.report.shop', compact('shopPaginate', 'request'));
    }

    public function product(Request $request)
    {
        if (!empty($request->get('start_date'))) {
            $from = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        } else {
            $from = Carbon::createFromFormat('d/m/Y', date('01/m/Y'));
        }

        if (!empty($request['end_date'])) {
            $to = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));
        } else {
            $to = Carbon::now();
        }

        $dataShop = OrderProduct::select(
            DB::raw('SUM(IF(orders.status_id IN (' . implode(",", STATUS_DON_HANG_CHOT) . '),orders.total_price,NULL)) as product_price'),
            'shops.id as shop_id',
            'shops.name as shop_name',
            'products.name as product_name'
        )
            ->join('orders', 'orders.id', 'order_products.order_id')
            ->join('products', 'products.id', 'order_products.product_id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->whereBetween('orders.close_date', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('products.id')
            ->get();

        $collection = collect($dataShop)->sortByDesc('product_price');
        if (isset($request['sort'])) {
            if ($request['direction'] == 'asc') {
                $collection = $collection->sortBy($request['sort']);
            }
            if ($request['direction'] == 'desc') {
                $collection = $collection->sortByDesc($request['sort']);
            }
        }

        $perPage = 20;
        $totalDataCollection = count($collection);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $collection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $shopPaginate = new LengthAwarePaginator($currentPageItems, $totalDataCollection, $perPage);
        $url = url()->current();

        $shopPaginate->setPath($url);

        return view('superadmin.report.product', compact('shopPaginate', 'from', 'to'));
    }

}
