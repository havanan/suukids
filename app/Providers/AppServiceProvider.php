<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Order;
use App\Models\Reminder;
use App\Models\User;
use App\Observers\CustomerObserver;
use App\Observers\ProductObserver;
use App\Observers\StockInObserver;
use App\Observers\StockOutObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Product::observe(ProductObserver::class);
        Customer::observe(CustomerObserver::class);
        StockIn::observe(StockInObserver::class);
        StockOut::observe(StockOutObserver::class);
        view()->composer('*', function ($view)
        {
            $total_day = 0;
            $total_month = 0;
            $total_completed_today = 0;
            $total_backorder_today = 0;
            $bxh = '';
            $percent = '';
            if (auth()->user()) {
                if (auth()->user()->isSale()) {
                    $total_day = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('assigned_user_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })
                    ->sum('total_price');
                    $total_month = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('assigned_user_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })
                    ->sum('total_price');
                    $total_prev = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01',strtotime('first day of last month')),date('Y-m-d',strtotime('-1 month')).' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('assigned_user_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })
                    ->sum('total_price');
                    $percent = $total_prev ? number_format($total_month / $total_prev*100) : '0';
                    $sale_ids = User::whereHas('permissions', function ($query) {
                        $query->where('sale_flag', 1);
                    })->onlyCurrentShop()->active()->pluck('id');
                    $totals = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q)use($sale_ids){
                        $q->whereIn('user_created',$sale_ids)
                        ->orWhereIn('assigned_user_id',$sale_ids);
                    })
                    ->select(DB::raw('sum(total_price) as sum,CASE WHEN assigned_user_id is NULL THEN user_created ELSE assigned_user_id END as user_id'))
                    ->groupBy(DB::raw('CASE WHEN assigned_user_id is NULL THEN user_created ELSE assigned_user_id END'))
                    ->orderBy('sum','desc')->get();
                    if ($total_day) {
                        $bxh = collect($totals)->pluck('user_id')->search(auth()->user()->id)+1;
                        $bxh .= '/' . collect($totals)->pluck('user_id')->count();
                    }
                    $total_completed_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('complete_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('user_created',auth()->user()->id)
                        ->orWhere('assigned_user_id',auth()->user()->id);
                    })->count();
                    $total_backorder_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('refund_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[REFUND_ORDER_STATUS_ID,RETURNED_STOCK_STATUS_ID])
                    ->where(function($q){
                        $q->where('user_created',auth()->user()->id)
                        ->orWhere('assigned_user_id',auth()->user()->id);
                    })->count();

                } else if (auth()->user()->isOnlyMarketing()) {
                    $total_day = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function ($q) {
                        $q->where('upsale_from_user_id', auth()->user()->id)
                        ->orWhere('marketing_id', auth()->user()->id)
                        ->orWhere('user_created', auth()->user()->id);
                    })->sum('total_price');
                    $total_month = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function ($q) {
                        $q->where('upsale_from_user_id', auth()->user()->id)
                        ->orWhere('marketing_id', auth()->user()->id)
                        ->orWhere('user_created', auth()->user()->id);
                    })->sum('total_price');
                    $total_prev = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01',strtotime('first day of last month')),date('Y-m-d',strtotime('-1 month')).' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('upsale_from_user_id',auth()->user()->id)
                        ->orWhere('marketing_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })
                    ->sum('total_price');
                    $percent = $total_prev ? number_format($total_month / $total_prev*100) : '0';
                    $mkt_ids = User::whereHas('permissions', function ($query) {
                        $query->where('marketing_flag', 1);
                    })->onlyCurrentShop()->active()->pluck('id');
                    $totals = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q)use($mkt_ids){
                        $q->whereIn('upsale_from_user_id',$mkt_ids)
                        ->orWhereIn('marketing_id',$mkt_ids)
                        ->orWhereIn('user_created',$mkt_ids);
                    })
                    ->select(DB::raw('sum(total_price) as sum,coalesce(upsale_from_user_id,marketing_id,user_created) as user_id'))
                    ->groupBy(DB::raw('coalesce(upsale_from_user_id,marketing_id,user_created)'))
                    ->orderBy('sum','desc')->get();
                    if ($total_day) {
                        $bxh = collect($totals)->pluck('user_id')->search(auth()->user()->id)+1;
                        $bxh .= '/' . collect($totals)->pluck('user_id')->count();
                    }
                    $total_completed_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('complete_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function($q){
                        $q->where('upsale_from_user_id',auth()->user()->id)
                        ->where('marketing_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })->count();
                    $total_backorder_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('refund_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[REFUND_ORDER_STATUS_ID,RETURNED_STOCK_STATUS_ID])
                    ->where(function($q){
                        $q->where('upsale_from_user_id',auth()->user()->id)
                        ->where('marketing_id',auth()->user()->id)
                        ->orWhere('user_created',auth()->user()->id);
                    })->count();
                } else {
                    $total_day = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->sum('total_price');
                    $total_month = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->sum('total_price');
                    $total_prev = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-01',strtotime('first day of last month')),date('Y-m-d',strtotime('-1 month')).' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->sum('total_price');
                    $percent = $total_prev ? number_format($total_month / $total_prev*100) : '0';
                    $total_completed_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('complete_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->count();
                    $total_backorder_today = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('refund_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[REFUND_ORDER_STATUS_ID,RETURNED_STOCK_STATUS_ID])->count();
                }
            }
            $view->with('shared_bxh', $bxh);
            $view->with('shared_percent', $percent);
            $view->with('shared_dstn', $total_month);
            $total = 0;
            if (auth()->user()) {
                if (auth()->user()->isSale()) {
                    $total = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function ($q) {
                        $q->where('assigned_user_id', auth()->user()->id)
                        ->orWhere('user_created', auth()->user()->id);
                    })->sum('total_price');
                } else if (auth()->user()->isOnlyMarketing()) {
                    $total = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])
                    ->where(function ($q) {
                        $q->where('upsale_from_user_id', auth()->user()->id)
                        ->orWhere('marketing_id', auth()->user()->id)
                        ->orWhere('user_created', auth()->user()->id);
                    })->sum('total_price');
                } else {
                    $total = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')->whereBetween('close_date',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])->whereIn('status_id',[DELIVERY_ORDER_STATUS_ID,CLOSE_ORDER_STATUS_ID,COMPLETE_ORDER_STATUS_ID,COLLECT_MONEY_ORDER_STATUS_ID])->sum('total_price');
                }
            }
            $view->with('shared_dshn', $total);
            $total = 0;
            if (auth()->user()) {
                if (auth()->user()->isSale()) {

                } else if (auth()->user()->isOnlyMarketing()) {
                    $costs = json_decode(auth()->user()->mkt_cost,true) ?: [];
                    foreach($costs as $source_id => $cost) {
                        foreach($cost as $date=>$amount) {
                            if (is_numeric($amount))
                            $total += $amount;
                        }
                    }
                } else {

                }
            }
            $view->with('shared_mttn', $total);
            $total = 0;
            if (auth()->user()) {
                $total = Reminder::where('created_by',auth()->user()->id)->whereBetween('created_at',[date('Y-m-01'),date('Y-m-t').' 23:59:59'])->where('is_completed','<>', 1)->count();
            }
            $view->with('shared_lhhn', $total);
            $total = 0;
            $view->with('shared_dtchn', $total_completed_today);
            $view->with('shared_dhhn', $total_backorder_today);
        });
    }
}
