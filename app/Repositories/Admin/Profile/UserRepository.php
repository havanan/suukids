<?php

namespace App\Repositories\Admin\Profile;

use App\Models\Order;
use App\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        $sales = $this->model::query()->onlyCurrentShop()->active()->get();
        return $sales;
    }

    //Get Active Sales
    public function getSales()
    {
        $sales = $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('sale_flag', 1);
        })->onlyCurrentShop()->active()->get();
        return $sales;
    }

    public function getMarketings()
    {
        return $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('marketing_flag', 1);
        })->onlyCurrentShop()->active()->get();
    }
    public function getArrSales()
    {
        return $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('sale_flag', 1);
        })->onlyCurrentShop()->pluck('name', 'id')->toArray();
    }
    public function getActiveArrSales()
    {
        return $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('sale_flag', 1);
        })->active()->onlyCurrentShop()->pluck('name', 'id')->toArray();
    }

    public function getArrMarkers()
    {
        return $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('marketing_flag', 1);
        })->onlyCurrentShop()->pluck('name', 'id')->toArray();
    }

    public function getActiveArrMarkers()
    {
        // if (!auth()->user()->isAdmin()) {
        //     return auth()->user()->marketer_members()->pluck('name','id')->toArray();
        // }
        return $this->model::query()->whereHas('permissions', function ($query) {
            $query->where('marketing_flag', 1);
        })->active()->onlyCurrentShop()->pluck('name', 'id')->toArray();
    }

    public function getSalePercentByMktDataQuery($params)
    {
        $query = Order::where('orders.shop_id', auth()->user()->shop_id)
        ;
        if (!empty($params['from'])) {
            $query = $query->where('orders.create_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (!empty($params['to'])) {
            $query = $query->where('orders.create_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
        $query = $query
        // ->whereNull('orders.duplicated')
        ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where(function ($q) use ($params) {
                $q->where('orders.assigned_user_id', $params['assigned_user_id']);
//                    ->orWhere('orders.assigned_user_id',$params['assigned_user_id']);
            })
        ;
        return $query;
    }
    public function getSalePercentByMktDataV2($params)
    {
        $sum_total = isset($params['price_total']['sum_order']) ? $params['price_total']['sum_order'] : 0;
        $query = $this->getSalePercentByMktDataQuery($params);
        $data = $query->join('users as m', 'm.id', 'orders.upsale_from_user_id')->select(
            DB::raw("m.name"),
            DB::raw("m.id")
            ,
            DB::raw('Sum(orders.total_price) as sum_price'),
            DB::raw("Count(orders.id) as count_order"),
            DB::raw("Sum(orders.total_price) /" . $sum_total . " * 100 as percent")

        )
            ->groupBy('m.id', 'm.name')
            ->get();
        $marketer = $params['marketer'];
        $default_mkt_data = $this->getDefaultMkt($marketer);
        if ($data->count() > 0) {
            foreach ($data as $item) {
                if (isset($default_mkt_data[$item->id])) {
                    $default_mkt_data[$item->id] = $this->makeItemMkt($item->name, $item->id, $item->count_order, $item->sum_price, $item->percent);
                }
            }
        }
        $old_ctm = $this->getOldCustomerMoney($params);
        $hotline = $this->getHotlineMoney($params);
        if ($old_ctm != null) {
            $default_mkt_data[-2] = $this->makeItemMkt('Khách cũ', -2, $old_ctm->count_old, $old_ctm->sum_old, $old_ctm->percent);
        }
        if ($hotline != null) {
            $default_mkt_data[-1] = $this->makeItemMkt('Hotline', -1, $hotline->count_hotline, $hotline->sum_hotline, $hotline->percent);
        }
//        dd($default_mkt_data);
        return $default_mkt_data;
    }
    public function getMktPercentBySaleData($params)
    {
        $sum_total = isset($params['price_total']['sum_order']) ? $params['price_total']['sum_order'] : 0;
        $query = Order::leftJoin('users', 'users.id', 'assigned_user_id')
        // ->whereNull('orders.duplicated')
            ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.shop_id', auth()->user()->shop_id)
            ->where(function ($q) use ($params) {
                $q->where('orders.upsale_from_user_id', $params['marketing_id'])
                    ->orWhere('orders.marketing_id', $params['marketing_id'])->orWhere('orders.user_created', $params['marketing_id']);
            });
        if (!empty($params['from'])) {
            $query = $query->where('orders.create_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (!empty($params['to'])) {
            $query = $query->where('orders.create_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
//        dd($query->pluck('orders.code')->toArray());
        $data = $query->select(
            'users.name', 'users.id',
            DB::raw('Count(orders.id) as count_order'),
            DB::raw('Sum(orders.total_price) as sum_order'),
            DB::raw("Sum(orders.total_price) /" . $sum_total . " * 100 as percent")
        )
            ->groupBy('users.name', 'users.id')
            ->get();
        $hotline = $query->whereNull('upsale_from_user_id')
            ->select(
                DB::raw("Sum(orders.total_price) as sum_hotline"),
                DB::raw("Count(orders.id) as count_hotline"),
                DB::raw("Sum(orders.total_price) /" . $sum_total . " * 100 as percent")
            )
            ->groupBy()
            ->first();

        $default_data = $this->getDefaultDataSale($params['sale']);
        if ($data->count() > 0) {
            foreach ($data as $item) {
                if (isset($default_data[$item->id])) {
                    $default_data[$item->id] = $this->makeItemSale($item->name, $item->id, $item->count_order, $item->sum_order, $item->percent);
                }
            }
        }
        if ($hotline != null) {
            $default_data[-1] = $this->makeItemSale('Hotline', -1, $hotline->count_order, $hotline->sum_order, $hotline->percent);
        }
//        dd($default_data);
        return $default_data;
    }
    //tính tiền hotline
    public function getHotlineMoney($params)
    {
        $sum_total = isset($params['price_total']['sum_order']) ? $params['price_total']['sum_order'] : 0;
        $query = $this->getSalePercentByMktDataQuery($params);
        $data = $query->leftJoin('users as m', 'm.id', 'orders.upsale_from_user_id')->whereNull('upsale_from_user_id')->select(
            DB::raw("Sum(orders.total_price) as sum_hotline"),
            DB::raw("Count(orders.id) as count_hotline"),
            DB::raw("Sum(orders.total_price) /" . $sum_total . " * 100 as percent")
        )
            ->groupBy('orders.customer_id')->first();
        return $data;
    }
    //tính tiền khách cũ đã mua của từng sale
    public function getOldCustomerMoney($params)
    {
        $sum_total = isset($params['price_total']['sum_order']) ? $params['price_total']['sum_order'] : 0;
        $query = $this->getSalePercentByMktDataQuery($params);
        $data = $query->leftJoin('users as m', 'm.id', 'orders.upsale_from_user_id')->join('customers', 'customers.id', 'orders.customer_id')
            ->where('orders.is_old_customer', ACTIVE)
            ->select(
                DB::raw("Sum(orders.total_price) as sum_old"),
                DB::raw("Count(orders.id) as count_old"),
                DB::raw("Sum(orders.total_price) /" . $sum_total . " * 100 as percent")
            )
            ->groupBy('customers.id')->first();
        return $data;
    }
    public function getDefaultMkt($marketer)
    {
        $data = array();
        if (empty($marketer)) {
            return $data;
        }
        foreach ($marketer as $key => $value) {
            $data[$key] = $this->makeItemMkt($value, $key, 0, 0, 0);
        }
        $data[-1] = $this->makeItemMkt('Hotline', -1, 0, 0, 0);
        $data[-2] = $this->makeItemMkt('Khách cũ', -2, 0, 0, 0);
        return $data;
    }

    public function makeItemMkt($name, $id, $count_order, $count_sale, $percent)
    {
        return [
            'name' => $name,
            'id' => $id,
            'count_order' => $count_order,
            'count_sale' => $count_sale,
            'sum_old' => $count_sale,
            'percent' => $percent,
        ];
    }
    public function getSaleRevenue($params)
    {
        $query = User::join('orders', 'orders.assigned_user_id', 'users.id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->where('orders.assigned_user_id', '!=', 'shops.owner_id')
            ->where('orders.assigned_user_id', $params['assigned_user_id'])
            ->whereIN('orders.status_id', STATUS_DON_HANG_CHOT)
        // ->whereNull('orders.duplicated')
            ->where('orders.shop_id', getCurrentUser()->shop_id);
        if (!empty($params['from'])) {
            $query = $query->where('orders.create_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (!empty($params['to'])) {
            $query = $query->where('orders.create_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
        $query = $query->select('users.name', 'users.id',
            DB::raw('Sum(orders.total_price) as sum_order')
        )
            ->groupBy('users.name', 'users.id')
            ->first();
        if ($query != null) {
            $query = $query->toArray();
        }
        return $query;
    }

    //get data doanh thu sale
    public function getReportSaleRevenue($params)
    {
        $query = array();
        if (isset($params['status_ids']) && !empty($params['status_ids'])) {
            foreach ($params['status_ids'] as $item) {
                $q = 'COUNT(IF(orders.status_id = ' . $item . ',1,NULL)) count_' . $item . ',';
                $q .= 'SUM(IF(orders.status_id = ' . $item . ',orders.total_price,0)) sum_' . $item;
                array_push($query, $q);
            }
        }
        $type_date = isset($params['type_date']) ? $params['type_date'] : 'close_date';
        return ['close_date' => $this->queryReportSaleRevenue($query, $params, $type_date), 'total' => $this->queryReportSaleRevenue($query, $params, 'all')];
    }

    private function queryReportSaleRevenue($query, $params, $type = 'close_date')
    {
        $data = Order::where('orders.shop_id', auth()->user()->shop_id)
            ->join('users', function ($join) use ($params) {
                $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                    ->whereNull('users.deleted_at');
            })
            ->where(function ($q) use ($params, $type) {
                if ($type == 'created_at') {
                    $q->whereBetween('orders.created_at', [$params['create_date_from'], $params['create_date_to']]);
                }
                if ($type == 'close_date') {
                    $q->whereBetween('orders.close_date', [$params['create_date_from'], $params['create_date_to']]);
                }
                if ($type == 'all') {
                    $q->whereBetween('orders.close_date', [$params['create_date_from'], $params['create_date_to']])
                        ->orWhereBetween('orders.share_date', [$params['create_date_from'], $params['create_date_to']]);
                }
            });

        if (isset($params['order_type'])) {
            $data->where('orders.type', '=', $params['order_type']);
        }
        if (isset($params['upsale_from_user_id'])) {
            $data->where('orders.upsale_from_user_id', '=', $params['upsale_from_user_id']);
        }
        if (isset($params['source_id'])) {
            $data->where('orders.source_id', '=', $params['source_id']);
        }
        if (isset($params['user_groups']) && $params['user_groups'] != null) {
            $data->where('users.user_group_id', $params['user_groups']);
        }
        if (isset($params['user_type']) && $params['user_type'] != null) {
            $data->where('users.status', $params['user_type']);
        }

        $string = implode(',', $query);
        $data->select('users.name as user_name', 'users.id as user_id', 'users.account_id',
            DB::raw('SUM(IF(orders.status_id = ' . DELIVERY_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . CLOSE_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . COMPLETE_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ' ,orders.total_price,0)) sum_total'),
            DB::raw('COUNT(IF(orders.status_id = ' . DELIVERY_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . CLOSE_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . COMPLETE_ORDER_STATUS_ID .
                ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ' ,1,NULL)) count_total'),
            DB::raw('COUNT(orders.id) order_total_user'))
            ->selectRaw($string)
            ->whereIn('users.id', array_keys($params['userSale']))
            ->groupBy('users.account_id');
        $data = $data->get();
        $result = [];
        foreach ($params['userSale'] as $userId => $userName) {
            $entity = $data->filter(function ($item) use ($userId) {
                return $item->user_id == $userId;
            })->first();
            $result[] = $entity ? $entity : [];
        }
        return $result;
    }

    //get data báo cáo tổng hợp sale
    public function getReportAggregateSale($params)
    {
        $query = array();
        if (isset($params['status_ids']) && !empty($params['status_ids'])) {
            foreach ($params['status_ids'] as $id => $item) {
                $q = 'COUNT(IF(orders.status_id = ' . $id . ',1,NULL)) count_' . $id . ',';
                $q .= 'SUM(IF(orders.status_id = ' . $id . ',orders.total_price,0)) sum_' . $id . '';
                array_push($query, $q);
            }
        }

        $selectType = 'COUNT(IF(orders.type = ' . ORDER_TYPE_NEW . ',1,NULL)) count_new_order ,';
        $selectType .= 'SUM(IF(orders.type = ' . ORDER_TYPE_NEW . ',total_price,NULL)) sum_new_order ,';

        $selectType .= 'COUNT(IF(orders.type = ' . ORDER_TYPE_CARE . ',1,NULL)) count_care_order ,';
        $selectType .= 'SUM(IF(orders.type = ' . ORDER_TYPE_CARE . ',total_price,NULL)) sum_care_order ,';

        $selectType .= 'COUNT(IF(orders.type = ' . ORDER_TYPE_OPTIMAL . ',1,NULL)) count_optimal_order ,';
        $selectType .= 'SUM(IF(orders.type >= ' . ORDER_TYPE_AGAIN . ',total_price,NULL)) sum_again_order ,';

        $selectType .= 'COUNT(IF(orders.status_id NOT IN (1,2,8),1,NULL)) count_access_order '; //tiếp cận thành công
        array_push($query, $selectType);
        $builder = User::leftJoin('orders', function ($join) use ($params) {
            $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                ->whereIn('orders.status_id', array_keys($params['status_ids']))
                ->where('orders.shop_id', getCurrentUser()->shop_id)
                ->whereNull('orders.deleted_at')
                ->where(function ($q) use ($params) {
                    $q->whereBetween('orders.share_date', [$params['create_date_from'], $params['create_date_to']]);
                    // ->orWhereBetween('orders.close_date', [$params['create_date_from'], $params['create_date_to']]);
                });
        })
        // ->leftJoin('order_status', 'order_status.id', 'orders.status_id')
            ->whereIn('users.id', array_keys($params['userSale']));
        if (isset($params['user_groups']) && $params['user_groups'] != null) {
            $builder = $builder->where('users.user_group_id', $params['user_groups']);
        }
        if (isset($params['user_type']) && $params['user_type'] != null) {
            $builder = $builder->where('users.status', $params['user_type']);
        }
        $string = implode(",", $query);
        $builder = $builder->select('users.name as user_name', 'users.id as user_id', 'users.account_id', DB::raw('SUM(orders.total_price) sum_total'), DB::raw('COUNT(orders.status_id) count_total'))
            ->selectRaw($string)
            ->groupBy('users.account_id', 'users.name', 'users.id')->get();
        return $builder;
    }

    //get ti le sale
    public function getReportOrderRate($params, $type)
    {
        $string = 'COUNT(IF(orders.status_id = ' . CANCEL_ORDER_STATUS_ID . ',1,NULL)) count_cancel_order ,';
        // $string = 'COUNT(IF(orders.status_id = ' . COMPLETE_ORDER_STATUS_ID . ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ',1,NULL)) count_close_real_order ,';
        $string .= 'COUNT(IF(orders.status_id = ' . DELIVERY_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . CLOSE_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . COMPLETE_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . ACCOUNTANT_DEFAULT_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . REFUND_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . RETURNED_STOCK_STATUS_ID .
            ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ' ,1,NULL)) count_close_order ,';
        $string .= 'COUNT(IF(orders.status_id NOT IN (1,2,8),1,NULL)) count_access_order,'; //tiếp cận thành công
        $string .= 'SUM(IF(orders.status_id != ' . CANCEL_ORDER_STATUS_ID . ',total_price,NULL)) sum_total_order '; //doanhh thu

        $builder = User::leftJoin('orders', function ($join) use ($params, $type) {
            $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                ->where('orders.shop_id', getCurrentUser()->shop_id)
                ->whereNull('orders.deleted_at');
            if ($type == 1) {
                $join->whereRaw('Date(orders.created_at) = CURDATE()');
            }

            if ($type == 2) {
                $join->whereDate('orders.created_at', \Carbon\Carbon::now()->addDay(-1));
            }

            if ($type == 3) {
                $join->whereBetween('orders.created_at', [$params['create_date_from'], $params['create_date_to']]);
            }

            if ($type == 4) {
                $join->whereBetween('orders.created_at', [$params['create_date_from_pre'], $params['create_date_to_pre']]);
            }

        });
        $builder = $builder->whereIn('users.id', array_keys($params['sales']));
        $builder = $builder->select('users.name as user_name', 'users.id as user_id', 'users.account_id', DB::raw('COUNT(orders.id) count_total'))
            ->selectRaw($string)
            ->groupBy('users.account_id', 'users.name', 'users.id')->get();
        return $builder;
    }
    //get kho so sale
    public function getReportWarehouseSaleNumber($params)
    {
        // $string = 'COUNT(IF(orders.status_id = ' . COMPLETE_ORDER_STATUS_ID . ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ',1,NULL)) count_close_real_order ,';
        $string = 'COUNT(IF(orders.status_id NOT IN (1,2,8),1,NULL)) count_access_order,';
        $string .= 'COUNT(IF(orders.status_id <> ' . NO_PROCESS_ORDER_STATUS_ID . ',1,NULL)) count_called_order ,';
        $string .= 'COUNT(IF(orders.status_id = ' . DELIVERY_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . CLOSE_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . COMPLETE_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . ACCOUNTANT_DEFAULT_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . REFUND_ORDER_STATUS_ID .
            ' OR orders.status_id = ' . RETURNED_STOCK_STATUS_ID .
            ' OR orders.status_id = ' . COLLECT_MONEY_ORDER_STATUS_ID . ' ,1,NULL)) count_close_order, ';
        $string .= 'COUNT(orders.assigned_user_id) as count_share';

        $builder = User::leftJoin('orders', function ($join) use ($params) {
            $join->whereRaw('(users.id = orders.assigned_user_id OR users.id = orders.user_created)')
                ->where('orders.shop_id', getCurrentUser()->shop_id)
                ->where('users.status', 1)
                ->whereNull('orders.deleted_at')
                ->where(function ($q) use ($params) {
                    $q->whereBetween('orders.share_date', [$params['create_date_from'], $params['create_date_to']]);
                    // ->orWhereBetween('orders.close_date', [$params['create_date_from'], $params['create_date_to']]);
                });
            if (isset($params['order_type'])) {
                $join->where('orders.type', '=', $params['order_type']);
            }

        })
            ->whereIn('users.id', array_keys($params['sales']))
            ->where('users.status', ACTIVE);

        $builder = $builder->select('users.name as user_name', 'users.id as user_id', 'users.account_id', DB::raw('COUNT(orders.id) count_total'))
            ->selectRaw($string)
            ->groupBy('users.account_id', 'users.name', 'users.id')->get();

        // dd($builder[0]);
        return $builder;
    }
    public function getDefaultDataSale($sales)
    {
        $default_data = array();
        if (empty($sales)) {
            return $default_data;
        }
        foreach ($sales as $key => $item) {
            $default_data[$key] = $this->makeItemSale($item, $key, 0, 0, 0);
        }
        $default_data[-1] = $this->makeItemSale('Hotline', -1, 0, 0, 0);
        return $default_data;
    }
    public function makeItemSale($name, $id, $count_order, $sum_order, $percent)
    {
        return [
            'name' => $name,
            'id' => $id,
            'count_order' => $count_order,
            'sum_order' => $sum_order,
            'percent' => $percent,
        ];
    }
}
