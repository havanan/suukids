<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderSource;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\Province;
use App\Models\Shop;
use App\Models\User;
use App\Models\MarketingCost;
use App\Models\UserGroup;
use App\Repositories\Admin\Product\ProductBundleRepository;
use App\Repositories\Admin\Profile\UserRepository;
use App\Repositories\Admin\Sell\DeliveryMethodRepository;
use App\Repositories\Admin\Sell\OrderProductRepository;
use App\Repositories\Admin\Sell\OrderRepository;
use App\Repositories\Admin\Sell\OrderSourceRepository;
use App\Repositories\Admin\Warehouse\StockGroupRepository;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $userRepository;
    protected $orderRepository;
    protected $stockGroupRepository;
    protected $orderSourceRepository;
    protected $orderProductRepository;
    protected $productBundleRepository;
    protected $deliveryMethodRepository;

    public function __construct(
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        StockGroupRepository $stockGroupRepository,
        OrderSourceRepository $orderSourceRepository,
        OrderProductRepository $orderProductRepository,
        ProductBundleRepository $productBundleRepository,
        DeliveryMethodRepository $deliveryMethodRepository
    ) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->stockGroupRepository = $stockGroupRepository;
        $this->orderSourceRepository = $orderSourceRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->productBundleRepository = $productBundleRepository;
        $this->deliveryMethodRepository = $deliveryMethodRepository;
    }

    public function index()
    {
        return view('admin.report.index');
    }

    public function employeeTurnover(Request $request)
    {

        $user_groups = UserGroup::pluck('name', 'id')->toArray();
        $orderSources = OrderSource::pluck('name', 'id')->toArray();
        $orderType = OrderType::pluck('name', 'id')->toArray();
        $orderStatus = OrderStatus::pluck('name', 'id')->toArray();
        $userLogin = auth()->user();
        // dd($userLogin);
        $usersUpSale = $this->userRepository->getArrMarkers();
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d 23:59:59'),
            'type_date' => request()->input('type_date') ?: 'close_date'
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
        $conditions['status_ids'] = [
            CLOSE_ORDER_STATUS_ID, CANCEL_ORDER_STATUS_ID,
            ACCOUNTANT_DEFAULT_ORDER_STATUS_ID, DELIVERY_ORDER_STATUS_ID,
            REFUND_ORDER_STATUS_ID, COMPLETE_ORDER_STATUS_ID, COLLECT_MONEY_ORDER_STATUS_ID, RETURNED_STOCK_STATUS_ID,
        ];

        $ownerShop = Shop::find($userLogin->shop_id);
        $salesUser = $this->userRepository->getArrSales();
        if (isset($salesUser[$ownerShop->owner_id])) {
            unset($salesUser[$ownerShop->owner_id]);
        }

        $conditions['userSale'] = $salesUser;
        $data = $this->userRepository->getReportSaleRevenue($conditions);
        // dd($data);
        return view('admin.report.employee_turnover', compact('user_groups', 'orderSources', 'orderType', 'usersUpSale', 'orderStatus', 'conditions', 'userLogin', 'data'));
    }

    public function orderRate(Request $request)
    {
        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
            'create_date_from_pre' => date('Y-m-d 00:00:00', strtotime(date('Y-m-01') . " -1 month")),
            'create_date_to_pre' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -1 month")),
        ];
        $userLogin = Auth::user();
        $ownerShop = Shop::find($userLogin->shop_id);
        $sales = $this->userRepository->getArrSales();
        if (isset($sales[$ownerShop->owner_id])) {
            unset($sales[$ownerShop->owner_id]);
        }

        $usersList = User::whereIn('id', array_keys($sales))->get();
        $orderStatusRevenue = OrderStatus::where('no_revenue_flag', INACTIVE)->pluck('name', 'id')->toArray();

        $conditions['sales'] = $sales;
        $conditions['orderStatusRevenue'] = $orderStatusRevenue;
        // dd($orderStatusRevenue);
        if ($request->isMethod('post')) {
            $data = $request->all();
            if (isset($data['create_date_from'])) {
                $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->startOfDay();
                $conditions['create_date_from_pre'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->addMonths(-1)->startOfDay();
            }
            if (isset($data['create_date_to'])) {
                $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->endOfDay();
                $conditions['create_date_to_pre'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->addMonths(-1)->endOfDay();
            }
        }

        $dataToday = $this->userRepository->getReportOrderRate($conditions, 1);
        $dataYesterday = $this->userRepository->getReportOrderRate($conditions, 2);
        $dataMonth = $this->userRepository->getReportOrderRate($conditions, 3);
        $dataMonthPre = $this->userRepository->getReportOrderRate($conditions, 4);
        // dd($dataYesterday);
        return view('admin.report.order_rate', compact('conditions', 'userLogin', 'dataToday', 'dataYesterday', 'dataMonth', 'dataMonthPre'));
    }

    public function revenueByStatus()
    {
        return view('admin.report.revenue_by_status');
    }

    public function aggregateSale(Request $request)
    {

        $userLogin = auth()->user();
        $user_groups = UserGroup::pluck('name', 'id')->toArray();
        $orderStatus = OrderStatus::query()->currentShop()->pluck('name', 'id')->toArray();
        $ownerShop = Shop::find($userLogin->shop_id);
        $sales = $this->userRepository->getArrSales();
        if (isset($sales[$ownerShop->owner_id])) {
            unset($sales[$ownerShop->owner_id]);
        }

        $conditions = [
            'create_date_from' => date('Y-m-01 00:00:00'),
            'create_date_to' => date('Y-m-d H:i:s'),
        ];
        $conditions['userSale'] = $sales;
        $conditions['status_ids'] = $orderStatus;

        if ($request->isMethod('post')) {
            $data = $request->all();
            if (isset($data['create_date_from'])) {
                $conditions['create_date_from'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_from'])->startOfDay();
            }
            if (isset($data['create_date_to'])) {
                $conditions['create_date_to'] = Carbon::createFromFormat(config('app.date_format'), $data['create_date_to'])->endOfDay();
            }
            if (isset($data['user_groups'])) {
                $conditions['user_groups'] = $data['user_groups'];
            }
            if (isset($data['user_type'])) {
                $conditions['user_type'] = $data['user_type'];
            }
        }

        $data = $this->userRepository->getReportAggregateSale($conditions);

        $date = Carbon::parse($conditions['create_date_from']);
        $now = Carbon::parse($conditions['create_date_to']);

        $diffDay = $date->diffInDays($now);
        return view('admin.report.aggregate_sale', compact('data', 'orderStatus', 'user_groups', 'conditions', 'diffDay', 'userLogin'));
    }

    public function evaluationCustomerCare()
    {
        return view('admin.report.evaluation_customer_care');
    }

    public function changeStatus()
    {
        return view('admin.report.change_status');
    }
    public function marketingStage()
    {
        $filters = request()->all();
        if (request()->ajax()){
            $data = \App\Models\Order::whereNull('deleted_at')->where('shop_id', getCurrentUser()->shop_id)
            ->whereBetween(request()->input('type_date')?:'created_at', [Carbon::createFromFormat('d/m/Y', request()->input('from'))->startOfDay(), Carbon::createFromFormat('d/m/Y', request()->input('to'))->endOfDay()])
            ->select(['id','total_price','status_id','marketing_id','user_created','upsale_from_user_id'])->get();
            return response()->json(['success'=>true,'data'=>$data]);
        }
        $marketers = $this->userRepository->getActiveArrMarkers();
        return view('admin.report.marketing_stage',compact('filters','marketers'));
    }

    // Tỷ lệ chốt đơn theo member
    public function marketingRevenue(Request $request)
    {
        $order_types = OrderType::all();
        $group_types = UserGroup::all();
        $status_arr = ACC_STATUS;
        $status = $request->get('status') != null ? $request->get('status') : ACTIVE;
        $params = $request->only('order_type', 'status', 'user_group_id','type_date');
        $params['status'] = $status;
        $params['status_ids'] = [
            CLOSE_ORDER_STATUS_ID, CANCEL_ORDER_STATUS_ID,
            ACCOUNTANT_DEFAULT_ORDER_STATUS_ID, DELIVERY_ORDER_STATUS_ID,
            REFUND_ORDER_STATUS_ID, COMPLETE_ORDER_STATUS_ID, COLLECT_MONEY_ORDER_STATUS_ID,
        ];
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $data = $this->orderRepository->getReportMarketingRevenue($params);
        return view('admin.report.marketing_revenue', compact('order_types', 'group_types', 'status', 'status_arr', 'data'));
    }
    public function getMarketingCost(){
        if (request()->ajax()){
            $cost_data = [];
            try{
                $date_begin = \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('begin'))->startOfDay();
                $date_end = \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('end'))->endOfDay();
                $data = request()->input('items')?:[];
                $costs = json_decode(auth()->user()->mkt_cost,true)?:[];

                $init_costs = [];
                $total_cost = [];
                if (auth()->user()->isAdmin()){
                    $data = \App\Models\User::whereHas('permissions', function ($query) {
                        $query->where('marketing_flag', 1);
                    })->where('shop_id',auth()->user()->shop_id)->select(['id','name','mkt_cost'])->get();
                    if (request()->input('active_account') == 1) {
                        $data = \App\Models\User::whereHas('permissions', function ($query) {
                            $query->where('marketing_flag', 1);
                        })->where('shop_id',auth()->user()->shop_id)->where('status',1)->select(['id','name','mkt_cost'])->get();
                    }
                    if (request()->input('active_account') == 2) {
                        $data = \App\Models\User::whereHas('permissions', function ($query) {
                            $query->where('marketing_flag', 1);
                        })->where('shop_id',auth()->user()->shop_id)->where('status',0)->select(['id','name','mkt_cost'])->get();
                    }
                    if (request()->input('marketer_id')>0) {
                        $mkt_costs = MarketingCost::whereBetween('day',[$date_begin,$date_end])->where('type',1)->where('shop_id',auth()->user()->shop_id)->where('user_id',request()->input('marketer_id'))->select(['source_id','amount','day','user_id'])->get();
                    } else {
                        $mkt_costs = MarketingCost::whereBetween('day',[$date_begin,$date_end])->where('type',1)->where('shop_id',auth()->user()->shop_id)->select(['source_id','amount','day','user_id'])->get();
                    }
                    $init_cost_tmp = [];
                    foreach($mkt_costs as $k=>$v){
                        if (!isset($init_cost_tmp[$v->source_id])) $init_cost_tmp[$v->source_id] = 0;
                        $init_cost_tmp[$v->source_id] += floatval($v->amount);
                    }
                    $init_costs = $init_cost_tmp;
                    $costs = [];
                    foreach($data as $marketer){
                        if (request()->input('marketer_id')) {
                            if ($marketer['id'] != request()->input('marketer_id')) continue;
                        }
                        $cost = json_decode($marketer['mkt_cost'],true)?:[];
                        foreach($cost as $source_id => $val) {
                            foreach($val as $date=>$value) {
                                if (!isset($costs[$source_id])) $costs[$source_id] = [];
                                if (!isset($costs[$source_id][$date])) $costs[$source_id][$date] = 0;
                                if (is_numeric($value)) {
                                    $costs[$source_id][$date] += $value;
                                    if (
                                        \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('begin'))->startOfDay() <= \Carbon\Carbon::createFromFormat('d/m/Y',$date)->startOfDay()
                                        &&
                                        \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('end'))->startOfDay() >= \Carbon\Carbon::createFromFormat('d/m/Y',$date)->startOfDay()
                                    ) {
                                        if (!isset($cost_data[$source_id])) $cost_data[$source_id] = 0;
                                        $cost_data[$source_id] += is_numeric($value) ? floatval($value) : 0;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $mkt_costs = MarketingCost::whereBetween('day',[$date_begin,$date_end])->where('type',1)->where('user_id',auth()->user()->id)->select(['source_id','amount','day'])->get();
                    $init_cost_tmp = [];
                    foreach($mkt_costs as $k=>$v){
                        if (!isset($init_cost_tmp[$v->source_id])) $init_cost_tmp[$v->source_id] = 0;
                        $init_cost_tmp[$v->source_id] += $v->amount;
                    }
                    $init_costs = $init_cost_tmp;
                    $sources = OrderSource::all();
                    foreach($sources as $source) {
                        $cost_data[$source->id] = MarketingCost::whereBetween('day',[$date_begin,$date_end])->where('type',2)->where('shop_id',auth()->user()->shop_id)->where('source_id',$source->id)->where('user_id',auth()->user()->id)->sum('amount')?:0;
                    }
                }
                return response()->json(['success'=>true,'cost_data'=>$cost_data,'data'=>$data,'mkt_costs'=>$mkt_costs,'init_cost'=>$init_costs,'cost'=> $costs]);
            }catch(Exception $e){
                return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
            }
        }
        return redirect(abort(404));
    }
    public function updateInitialMarketingCost(){
        if (request()->ajax()){
            if (auth()->user()->isAdmin() && request()->input('marketing_id')) {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('date'));
                $source_id = request()->input('key');
                $amount = floatval(request()->input('value'));
                $exists = MarketingCost::where('source_id',$source_id)->where('type',1)->where('user_id',request()->input('marketing_id'))->where('day',$date->format('Y-m-d'))->where('shop_id',auth()->user()->shop_id)->first();
                if ($exists) {
                    MarketingCost::find($exists->id)->update(['amount'=>$amount]);
                    $exists = MarketingCost::where('source_id',$source_id)->where('type',1)->where('day',$date->format('Y-m-d'))->where('shop_id',auth()->user()->shop_id)->where('user_id',request()->input('marketing_id'))->first();
                } else {
                    MarketingCost::insert(['created_at'=>date('Y-m-d H:i:s'),'created_by'=>auth()->user()->id,'type'=>1,'user_id'=>request()->input('marketing_id'),'amount'=>$amount,'source_id'=>$source_id,'day'=>$date->format('Y-m-d'),'shop_id'=>auth()->user()->shop_id]);
                    $exists = MarketingCost::where('source_id',$source_id)->where('user_id',request()->input('marketing_id'))->where('type',1)->where('day',$date->format('Y-m-d'))->where('shop_id',auth()->user()->shop_id)->first();
                }
                return response()->json(['success'=>true,'exists'=>$exists,'msg'=>'Cập nhật thành công']);
            } else {
                return response()->json(['success'=>false,'msg'=>'Thao tác không được phép']);
            }
        }
        return redirect(abort(404));
    }
    public function fillMarketingCost(){
        $users = \App\Models\User::where('shop_id','>',0)->get();
        MarketingCost::where('shop_id','>',0)->delete();
        foreach($users as $user) {
            $costs = json_decode($user->mkt_cost,true)?:[];
            foreach($costs as $source_id => $cost) {
                foreach($cost as $day_key => $amount) {
                    $day_formatted = Carbon::createFromFormat('d/m/Y', $day_key)->startOfDay()->format('Y-m-d');
                    $exists = MarketingCost::where('source_id',$source_id)->where('type',2)->where('user_id',$user->id)->where('day',$day_formatted)->where('shop_id',$user->shop_id)->first();
                    if ($exists) {
                        MarketingCost::find($exists->id)->update(['amount'=>$amount]);
                    } else {
                        MarketingCost::insert(['created_at'=>date('Y-m-d H:i:s'),'created_by'=>auth()->user()->id,'type'=>2,'user_id'=>$user->id,'amount'=>$amount,'source_id'=>$source_id,'day'=>$day_formatted,'shop_id'=>$user->shop_id]);
                    }
                }
            }
            $costs = json_decode($user->init_cost,true)?:[];
            foreach($costs as $source_id => $cost) {
                foreach($cost as $day_key => $amount) {
                    $day_formatted = Carbon::createFromFormat('d/m/Y', $day_key)->startOfDay()->format('Y-m-d');
                    $exists = MarketingCost::where('source_id',$source_id)->where('type',1)->where('day',$day_formatted)->where('shop_id',$user->shop_id)->first();
                    if ($exists) {
                        MarketingCost::find($exists->id)->update(['amount'=>$amount]);
                    } else {
                        MarketingCost::insert(['created_at'=>date('Y-m-d H:i:s'),'created_by'=>auth()->user()->id,'type'=>1,'user_id'=>$user->id,'amount'=>$amount,'source_id'=>$source_id,'day'=>$day_formatted,'shop_id'=>$user->shop_id]);
                    }
                }
            }
        }
        return response()->json(['success'=>true,'count'=>[
            MarketingCost::where('type',1)->count(),
            MarketingCost::where('type',2)->count(),
        ]]);
    }
    public function updateMarketingCost(){
        if (request()->ajax()){
            if (date('H')>=12) {
                return response()->json(['success'=>false,'msg'=>"Không được phép sửa chi phí ADS sau 12h"]);
            }
            $date = \Carbon\Carbon::createFromFormat('d/m/Y',request()->input('date'));
            $now = \Carbon\Carbon::now();
            $diff = $date->diffInDays($now);
            if (auth()->user()->isAdmin()) {
                return response()->json(['success'=>false,'msg'=>"Chỉ có nhân viên mới được nhận chi phí ADS!"]);
            } else {
                if (!in_array($diff,[1,2,3]) || $date >= $now) {
                    return response()->json(['success'=>false,'diff'=>$diff,'msg'=>"Không được phép sửa chi phí ADS ngoài khoảng 3 ngày gần nhất"]);
                }
            }
            $costs = json_decode(auth()->user()->mkt_cost,true)?:[];
            $key = request()->input('key');
            $source = OrderSource::find($key);
            if ($source) {
                $date = request()->input('date');
                $value = request()->input('value');
                if (!isset($costs[$key])) $costs[$key] = [];
                if (!isset($costs[$key][$date])) $costs[$key][$date] = 0;
                $costs[$key][$date] = $value;
                \App\Models\User::find(auth()->user()->id)->update(['mkt_cost'=>json_encode($costs)]);
                $day_formatted = Carbon::createFromFormat('d/m/Y', $date)->startOfDay()->format('Y-m-d');
                $exists = MarketingCost::where('source_id',$source->id)->where('type',2)->where('user_id',auth()->user()->id)->where('day',$day_formatted)->where('shop_id',auth()->user()->shop_id)->first();
                if ($exists) {
                    MarketingCost::find($exists->id)->update(['amount'=>$value]);
                } else {
                    MarketingCost::insert(['created_at'=>date('Y-m-d H:i:s'),'created_by'=>auth()->user()->id,'type'=>2,'user_id'=>auth()->user()->id,'amount'=>$value,'source_id'=>$source->id,'day'=>$day_formatted,'shop_id'=>auth()->user()->shop_id]);
                }
                return response()->json(['success'=>true,'exists'=>$exists,'cost'=>$costs,'msg'=>"Cập nhật thành công"]);
            }else{
                return response()->json(['success'=>false,'msg'=>"Không tìm thấy nguồn"]);
            }
        }
        return redirect(abort(404));
    }
    //marketing theo nguồn
    public function marketingBySource(Request $request)
    {
        $marketers = \App\Models\User::whereHas('permissions', function ($query) {
            $query->where('marketing_flag', 1);
        })->where('shop_id',auth()->user()->shop_id)->get();
        $filters = request()->all();
        if (request()->ajax()){
            if (request()->input('active_account') == 1) {
                $marketers = \App\Models\User::whereHas('permissions', function ($query) {
                    $query->where('marketing_flag', 1);
                })->where('shop_id',auth()->user()->shop_id)->where('status',1)->get();
            }
            if (request()->input('active_account') == 2) {
                $marketers = \App\Models\User::whereHas('permissions', function ($query) {
                    $query->where('marketing_flag', 1);
                })->where('shop_id',auth()->user()->shop_id)->where('status',0)->get();
            }
            $params = [];
            $params['from'] = request()->has('from') != null ? request()->input('from') : date('01/m/Y');
            $params['to'] = request()->has('to') != null ? request()->input('to') : date('d/m/Y');
            $query = \App\Models\Order::where('orders.shop_id', getCurrentUser()->shop_id)->whereNull('orders.deleted_at');
            if (request()->input('marketing_id')) {
                $marketing_id = request()->input('marketing_id');
                $query = $query->where(function($query)use($marketing_id){
                    return $query->where('marketing_id', $marketing_id)
                    ->orWhere('upsale_from_user_id', $marketing_id)
                    ->orWhere('user_created', $marketing_id);
                });
            } else {
                $query = $query->where(function($query)use($marketers){
                    $ids = collect($marketers)->pluck('id')->all();
                    return $query->whereIn('marketing_id', $ids)
                    ->orWhereIn('upsale_from_user_id',$ids)
                    ->orWhereIn('user_created',$ids);
                });
            }

            if (!empty($params['from'])) {
                $query = $query->whereDate('orders.created_at', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            }
            if (!empty($params['to'])) {
                $query = $query->whereDate('orders.created_at', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            }
            $data = $query->get(['id','source_id','marketing_id','user_created','upsale_from_user_id','created_at','status_id','total_price']);
            return response()->json(['success'=>true,'marketers'=>$marketers,'data'=>$data]);
        }
        $sources = OrderSource::pluck('name','id')->toArray();

        $params = $request->only('bundle_id', 'marketing_id','active_account');
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $view_arr = MKT_SOURCE_VIEW_BY;
        $marketer = \App\Models\User::whereHas('permissions', function ($query) {
            $query->where('marketing_flag', 1);
        })->where('shop_id',auth()->user()->shop_id)->get();
        if (request()->input('active_account') == 1) {
            $marketer = \App\Models\User::whereHas('permissions', function ($query) {
                $query->where('marketing_flag', 1);
            })->where('shop_id',auth()->user()->shop_id)->where('status',1)->get();
        }
        if (request()->input('active_account') == 2) {
            $marketer = \App\Models\User::whereHas('permissions', function ($query) {
                $query->where('marketing_flag', 1);
            })->where('shop_id',auth()->user()->shop_id)->where('status',0)->get();
        }
        $productBundles = $this->productBundleRepository->all();
        $sources = $this->orderSourceRepository->pluckAll();
        $params['mkt_ids'] = collect($marketer)->pluck('id')->all();
        $data = $this->orderRepository->getMktSource($params, $sources);
        $tyLeChotMkt = $this->orderRepository->bieuDoTyLeChotMkt($marketer->pluck('account_id', 'id')->toArray(), $params);
        $tyLeCuaMkt = !empty($params['marketing_id']) ? User::find($params['marketing_id']) : [];


        return view('admin.report.marketing_by_source',compact('marketers','filters','params','sources','data','tyLeChotMkt','tyLeCuaMkt'));
    }
    //tỉ lệ chốt của sale theo data mkt
    public function salePercentByMktData(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }

        return view('admin.report.sale_percent_mkt_data');
    }

    //tỉ lệ chốt của sale theo data mkt
    public function getDatasalePercentByMktData(Request $request)
    {
        set_time_limit(0);
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $marketers = $this->userRepository->getActiveArrMarkers();
        $sales = $this->userRepository->getActiveArrSales();
        $data = $this->getSalePercentMktPercent($sales, $marketers, $params);
        return view('admin.report.element_sale_percent_mkt_data', compact('data', 'marketers', 'sales'));
    }

    // Sale theo MKT %
    public function getSalePercentMktPercent($sales, $marketers, $params)
    {
        $result = array();
        $result_item = [
            'hotline' => [
                "percent" => 0,
            ],
            'old_ctm' => [
                "percent" => 0,
            ],
        ];
        if (empty($sales)) {
            return $result;
        }
        foreach ($sales as $sale_id => $sale_name) {
            if (empty($marketers)) {
                $result[$sale_id] = $result_item;
            }
            $params['assigned_user_id'] = $sale_id;
            $params['total_order'] = $this->orderRepository->getSaleRevenue($params);
            foreach ($marketers as $id => $name) {
                $params['upsale_from_user_id'] = $id;
                $mkt[$id] = $this->orderRepository->getMktPercentBySalePercent($params);
            }
            $mkt[-1] = $this->orderRepository->getHotlineMoneyPercent($params);
            $mkt[-2] = $this->orderRepository->getOldCustomerMoneyPercent($params);
            $result[$sale_name] = $mkt;
        }
        return $result;
    }

    // tỷ lệ số mtk theo doanh thu sale
    public function mtkPercentBySaleData(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        return view('admin.report.mkt_percent_sale_data');
    }

    public function getDatamtkPercentBySaleData(Request $request)
    {
        set_time_limit(0);
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $marketers = $this->userRepository->getActiveArrMarkers();
        $sales = $this->userRepository->getActiveArrSales();
        $data = $this->getSalePercentMktDoanhThu($sales, $marketers, $params);
        $old_customer = $this->orderRepository->getOldCustomerByMkt($marketers, $params);
        return view('admin.report.element_mkt_percent_sale_data', compact('data', 'marketers', 'sales', 'old_customer'));
    }
    public function saleMktAlignment(Request $request){
        set_time_limit(0);
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $marketers = $this->userRepository->getActiveArrMarkers();
        $sales = $this->userRepository->getActiveArrSales();
        $data = \App\Models\Order::onlyCurrentShop()
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.close_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay())
            ->where('orders.close_date', '<', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay())
            ->select(['total_price','upsale_from_user_id','is_old_customer','user_created','assigned_user_id'])->get();
        $sale_orders = \App\Models\Order::onlyCurrentShop()
            ->whereIn('orders.status_id', STATUS_DON_HANG_CHOT)
            ->where('orders.share_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay())
            ->where('orders.share_date', '<', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay())
            ->select(['total_price','upsale_from_user_id','is_old_customer','user_created','assigned_user_id'])->get();
        return response()->json(['success'=>true,'sale_orders'=>$sale_orders,'data'=>$data,'sales'=>$sales,'marketers'=>$marketers]);
    }

    // Sale theo MKT doanh thu
    public function getSalePercentMktDoanhThu($sales, $marketers, $params)
    {
        $result = array();
        $result_item = [
            'hotline' => [
                "sum_order" => 0,
                "sum_price" => 0,
            ],
            'old_ctm' => [
                "sum_order" => 0,
                "sum_price" => 0,
            ],
        ];
        if (empty($sales)) {
            return $result;
        }
        foreach ($sales as $sale_id => $sale_name) {
            if (empty($marketers)) {
                $result[$sale_id] = $result_item;
            }
            $params['assigned_user_id'] = $sale_id;
            //tính tổng đơn đã chốt của sale hiện tại
            foreach ($marketers as $id => $name) {
                $params['upsale_from_user_id'] = $id;
                $mkt[$id] = $this->orderRepository->getMktPercentBySale($params);
            }
            $mkt[-1] = $this->orderRepository->getHotlineMoney($params);
            $mkt[-2] = $this->orderRepository->getOldCustomerMoney($params);
            $result[$sale_name] = $mkt;
        }
        return $result;
    }

    //biểu đồ lượng đơn theo trạng thái đơn hàng
    public function orderStatus(Request $request)
    {
        $params['month'] = $request->get('month') != null ? $request->get('month') : date('m');
        $params['year'] = $request->get('year') != null ? $request->get('year') : date('Y');
        $params['status'] = OrderStatus::whereNull('shop_id')->pluck('id')->toArray();
        $status = $this->getStatusPluck($params);
        $data = $this->orderRepository->getOrderStatus($params, $status);
        return view('admin.report.order_status', compact('data'));
    }
    //biểu đồ đơn hàng theo tỉnh thành
    public function province(Request $request)
    {
        $params['month'] = $request->get('month') != null ? $request->get('month') : date('m');
        $params['year'] = $request->get('year') != null ? $request->get('year') : date('Y');
        $default_province = Province::take(3)->pluck('_name', 'id');
        $params['provinces'] = $default_province != null ? $default_province : null;
        $data = $this->orderRepository->getProvinceChart($params);
        return view('admin.report.province', compact('data'));
    }
    //báo cáo doanh thu theo hình thức vận chuyển
    public function delivery(Request $request)
    {
        $deliveries = $this->deliveryMethodRepository->getArrAll();
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : Carbon::now()->format('d/m/Y');
        $params['delivery_id'] = $request->get('delivery_id');
        $params['status'] = [
            COMPLETE_ORDER_STATUS_ID, DELIVERY_ORDER_STATUS_ID, REFUND_ORDER_STATUS_ID,
        ];
        $data = $this->orderRepository->getSaleDelivery($params);
        return view('admin.report.delivery', compact('data', 'params', 'deliveries'));
    }
    //thống kê sp/hàng hóa
    public function product(Request $request)
    {

        $sales = $this->userRepository->getArrSales();
        $stock_groups = $this->stockGroupRepository->getAll();
        $status_groups = $this->getStatusPluck();
        $product_bundles = $this->productBundleRepository->getAll();
        $params = $this->getParamsProductReport($request);
        //        dd($params);
        $data = $this->orderProductRepository->getProductReport($params);
        return view('admin.report.product', compact('data', 'params', 'sales', 'stock_groups', 'status_groups', 'product_bundles'));
    }
    //thống kê doanh thu sp/hàng hóa
    public function productRevenue(Request $request)
    {
        $sales = $this->userRepository->getArrSales();
        $stock_groups = $this->stockGroupRepository->getAll();
        $status_groups = $this->getStatusPluck();
        $product_bundles = $this->productBundleRepository->getAll();
        $params = $this->getParamsProductReport($request);

        $data = $this->orderProductRepository->getProductReport($params);
        return view('admin.report.product_revenue', compact('data', 'params', 'sales', 'stock_groups', 'status_groups', 'product_bundles'));
    }
    public function overview() {
        set_time_limit(0);
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        $marketers = $this->userRepository->getActiveArrMarkers();
        $filters = request()->all();
        if (request()->ajax()){
            $params = [];
            $params['from'] = request()->has('from') != null ? request()->input('from') : date('01/m/Y');
            $params['to'] = request()->has('to') != null ? request()->input('to') : date('d/m/Y');

            $query = \App\Models\Order::where('orders.shop_id', getCurrentUser()->shop_id)->whereNull('orders.deleted_at');
            if (!empty($params['from'])) {
                $query = $query->whereDate('orders.created_at', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
            }
            if (!empty($params['to'])) {
                $query = $query->whereDate('orders.created_at', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
            }
            $data = $query->get(['id','total_price','upsale_from_user_id','marketing_id','assigned_user_id','user_created','status_id']);
            return response()->json(['success'=>true,'data'=>$data]);
        }

        return view('admin.report.overview',compact('marketers','filters'));
    }
    public function marketers(){
        $marketers = $this->userRepository->getMarketings();
        $wage = [];
        foreach($marketers as $key=>$val) {
            $marketers[$key]['labor'] = json_decode($marketers[$key]['labor'],true)?:[];
            $marketers[$key]['mkt_cost'] = json_decode($marketers[$key]['mkt_cost'],true)?:[];
            $marketers[$key]['bonus_percent'] = json_decode($marketers[$key]['bonus_percent'],true)?:[];
            $marketers[$key]['bonus'] = json_decode($marketers[$key]['bonus'],true)?:[];
            $wages = array_column(array_filter($marketers[$key]['labor'],function($ite){return isset($ite['wage'])&&$ite['wage']>0;}),'wage');
            $wage[$val['id']]=count($wages)?intval(max($wages)):0;
        }
        return response()->json(['success'=>true,'wage'=>$wage,'data'=>$marketers]);
    }
    public function updateMarketer() {
        set_time_limit(0);
        if (!Auth::user()->isAdmin()) {
            return redirect(abort(403));
        }
        $marketers = $this->userRepository->getActiveArrMarkers();
        $filters = request()->all();
        if (request()->ajax()){
            $marketer_id = request()->input('marketer_id');
            if (isset($marketers[$marketer_id])) {
                $marketer = \App\Models\User::findOrFail($marketer_id);
                $labor = json_decode($marketer->labor,true)?:[];
                $bonus_percent = json_decode($marketer->bonus_percent,true)?:[];
                $bonus = json_decode($marketer->bonus,true)?:[];
                $month = request()->input('key');
                $column = request()->input('column');
                $value = request()->input('value');
                if (in_array($column,['wage','labor_day'])) {
                    if (!array_has($labor,$month)) {
                        $labor[$month] = [
                            $column => $value
                        ];
                    } else{
                        $labor[$month][$column] = $value;
                    }
                } else if (in_array($column,['bonus'])) {
                    $bonus[$month] = $value;
                } else if (in_array($column,['bonus_percent'])) {
                    $bonus_percent[$month] = $value;
                }
                $marketer->labor = json_encode($labor);
                $marketer->bonus_percent = json_encode($bonus_percent);
                $marketer->bonus = json_encode($bonus);
                $marketer->save();
            }
            $marketer = \App\Models\User::findOrFail($marketer_id);
            $marketer->labor = json_decode($marketer->labor,true)?:[];
            $marketer->bonus_percent = json_decode($marketer->bonus_percent,true)?:[];
            $marketer->bonus = json_decode($marketer->bonus,true)?:[];
            $marketer->mkt_cost = json_decode($marketer->mkt_cost,true)?:[];
            return response()->json(['success'=>true,'data'=>$marketer]);
        }

        return [];
    }
    public function getStatusPluck($params = false)
    {
        $status = DB::table('order_status');
        if (isset($params['status']) && !empty($params['status'])) {
            $status = $status->whereIn('id', $params['status']);
        }
        $status = $status->where(function ($q) {
            $q->where('shop_id', auth()->user()->shop_id)->orWhere('shop_id', null);
        })->pluck('name', 'id');
        if ($status != null) {
            $status = $status->toArray();
        }
        return $status;
    }
    public function getParamsProductReport($request)
    {
        $params = $request->only('bundle_id', 'code', 'sale_id', 'stock_id');
        $params['from'] = $request->get('from') != null ? $request->get('from') : date('01/m/Y');
        $params['to'] = $request->get('to') != null ? $request->get('to') : date('d/m/Y');
        $params['status_id'] = $request->get('status_id');
        $params['has_revenue'] = $request->get('has_revenue') != null ? $request->get('has_revenue') : ACTIVE;
        return $params;
    }
}
