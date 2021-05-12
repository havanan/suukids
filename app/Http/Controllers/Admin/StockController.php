<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockGroup;
use DB;
use App\Repositories\Admin\Profile\UserRepository;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class StockController extends Controller
{
    protected $userRepository;
    //
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function warehouseAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            if ($this->save($data)) {
                $mess = 'Dữ liệu đã được update';
            } else {
                $mess = 'Thao tác thất bại';
            }

            return redirect()->route('admin.stock.warehouse.list')->with('success', $mess);
        }
        return view('admin.stock.add_warehouse');
    }

    public function warehouseEdit(Request $request, $id)
    {
        $warehouse = StockGroup::findOrFail($id);
        if ($request->isMethod('post')) {
            $data = $request->all();
            if ($this->save($data, $id)) {
                $mess = 'Dữ liệu đã được update';
            } else {
                $mess = 'Thao tác thất bại';
            }

            return redirect()->route('admin.stock.warehouse.list')->with('success', $mess);
        }
        return view('admin.stock.add_warehouse', ['warehouse' => $warehouse]);
    }

    public function save($data, $id = null)
    {
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'required' => 'Lỗi nhập :attribute ',

        ];
        $fieldNames = [
            'name' => 'tên',
        ];
        $validator = Validator::make($data, $rules, $messages, $fieldNames);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        DB::beginTransaction();
        try {
            if ($id) {
                $stock = StockGroup::find($id);
            } else {
                $stock = new StockGroup;
            }

            if ($stock) {
                $stock->name = $data['name'];
                $stock->shop_id = getCurrentUser()->shop_id;
                $stock->is_main = isset($data['is_main']) ? $data['is_main'] : INACTIVE;
                $stock->save();
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return true;
    }

    public function warehouseList(Request $request)
    {
        $listWarehouse = StockGroup::all();
        $mode = 'list';
        if ($request->isMethod('post')) {
            $data = $request->all();

            if (isset($data['selected_ids']) && !empty($data['selected_ids'])) {
                $listWarehouse = [];
                foreach ($data['selected_ids'] as $value) {
                    $stock = StockGroup::findOrFail($value);
                    $listWarehouse[] = $stock;
                }
                $mode = 'delete';
            }
        }
        return view('admin.stock.list_warehouse', ['listWarehouse' => $listWarehouse, 'mode' => $mode]);
    }
    public function product()
    {
        if (request()->ajax()){

            $marketers = $this->userRepository->getActiveArrMarkers();
            $params = [];
            $params['from'] = request()->has('from') != null ? request()->input('from') : date('Y-m-01');
            $params['to'] = request()->has('to') != null ? request()->input('to') : date('Y-m-d');
            $query = \App\Models\Order::where('orders.shop_id', getCurrentUser()->shop_id)
                ->whereNull('orders.deleted_at')
                ->whereIn('status_id',STATUS_DON_HANG_CHOT);
            if (request()->input('marketing_id')) {
                $marketing_id = request()->input('marketing_id');
                $query = $query->where(function($query)use($marketing_id){
                    return $query->where('marketing_id', $marketing_id)
                    ->orWhere('upsale_from_user_id', $marketing_id)
                    ->orWhere('user_created', $marketing_id);
                });
            } else {
                $query = $query->where(function($query)use($marketers){
                    return $query->whereIn('marketing_id', array_keys($marketers))
                    ->orWhereIn('upsale_from_user_id',array_keys($marketers))
                    ->orWhereIn('user_created',array_keys($marketers));
                });
            }

            if (!empty($params['from'])) {
                $query = $query->whereDate('orders.created_at', '>=', Carbon::createFromFormat('Y-m-d', $params['from'])->startOfDay());
            }
            if (!empty($params['to'])) {
                $query = $query->whereDate('orders.created_at', '<=', Carbon::createFromFormat('Y-m-d', $params['to'])->endOfDay());
            }
            $query = $query->orderBy('created_at','desc');
            $order_ids = $query->pluck('id')->all();
            $data = \App\Models\OrderProduct::whereIn('order_id',$order_ids)->get();
            $data = collect($data)->groupBy('product_id')->all();
            $res = [];
            foreach($data as $key=>$val) {
                $before_entity = \App\Models\Operation::where('product_id', $key)->whereBetween('created_at',[
                    Carbon::createFromFormat('Y-m-d', $params['from'])->startOfDay(),
                    Carbon::createFromFormat('Y-m-d', $params['to'])->endOfDay(),
                ])->orderBy('created_at','asc')->first();
                $before_quantity = 0;
                if ($before_entity) {
                    $before_quantity = $before_entity->before_quantity;
                } else {
                    $before_entity = \App\Models\Operation::where('product_id', $key)
                        ->where('created_at','<',Carbon::createFromFormat('Y-m-d', $params['from'])
                            ->startOfDay())
                        ->orderBy('created_at','desc')
                        ->first();
                    if ($before_entity) {
                        $before_quantity = $before_entity->before_quantity + $before_entity->quantity;
                    }
                }
                $op_in = \App\Models\Operation::where('product_id', $key)->whereBetween('created_at',[
                    Carbon::createFromFormat('Y-m-d', $params['from'])->startOfDay(),
                    Carbon::createFromFormat('Y-m-d', $params['to'])->endOfDay(),
                ])->sum('quantity');
                $res[] = [$key,$before_quantity*1,$op_in*1,count($val)];
            }
            return response()->json(['success'=>true,'data'=>$res,'count'=>count($data)]);
        }
        $products = collect(\App\Models\Product::where('shop_id',getCurrentUser()->shop_id)->active()->get());
        return view('admin.stock.product',compact('products'));
    }
    public function warehouseDelete(Request $request)
    {
        $data = $request->all();
        if (isset($data['selected_ids']) && !empty($data['selected_ids'])) {
            StockGroup::whereIn('id', $data['selected_ids'])->delete();
        }
        return redirect()->route('admin.stock.warehouse.list')->with('success', 'Xóa thành công');
    }
}
