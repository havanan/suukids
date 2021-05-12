<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomnerRequest;
use App\Models\CallHistory;
use App\Models\Customer;
use App\Models\CustomerLevel;
use App\Models\CustomerGroup;
use App\Models\NoteHistory;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\OrderSource;
use App\Models\Pathological;
use App\Models\Province;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function overview()
    {
        $query = Order::with(['source','customer','province','district','ward','products','order_products','order_products.product','status'])->latest();
        if (request()->input('source')) {
            $query = $query->where('source_id',request()->input('source'));
        }

        // if (!empty($param['assigned_user_id'])) {
        //     $assignUserId = $param['assigned_user_id'];
        //     if ($assignUserId == -1) {
        //         $query = $query->where(function ($q) use ($assignUserId) {
        //             $q->whereNull('assigned_user_id')
        //                 ->orWhereNull('user_created');
        //         });
        //     } else {
        //         $query = $query->where(function ($q) use ($assignUserId) {
        //             $q->where('assigned_user_id', $assignUserId)
        //                 ->orWhere('user_created', $assignUserId);
        //         });
        //     }

        // }

        // if (!empty($param['marketing_id'])) {
        //     $marketingId = $param['marketing_id'];
        //     $query = $query->where(function ($q) use ($marketingId) {
        //         $q->where('upsale_from_user_id', $marketingId)
        //             ->orWhere('marketing_id', $marketingId)
        //             ->orWhere('user_created', $marketingId);
        //     });
        // }

        // if (!empty($param['staff_id'])) {
        //     $staffId = $param['staff_id'];
        //     $query = $query->where(function ($q) use ($staffId) {
        //         $q->where('user_created', $staffId)
        //             ->orWhere('upsale_from_user_id', $staffId)
        //             ->orWhere('assigned_user_id', $staffId)
        //             ->orWhere('close_user_id', $staffId)
        //             ->orWhere('delivery_user_id', $staffId)
        //             ->orWhere('user_created', $staffId)
        //             ->orWhere('marketing_id', $staffId);
        //     });
        // }
        if (request()->input('assigned')) {
            if (request()->input('assigned') == -1) {
                $query = $query->where(function($q){
                    $q->whereNull('assigned_user_id')
                    ->orWhereNull('user_created');
                });
            } else {
                $query = $query->where(function($q){
                    $q->where('user_created', request()->input('assigned'))
                    ->orWwhere('assigned_user_id', request()->input('assigned'));
                });
            }
        }
        if (request()->input('marketing')) {
            $query = $query->where(function($q){
                $q->where('user_created', request()->input('marketing'))
                ->orWwhere('upsale_from_user_id', request()->input('marketing'))
                ->orWwhere('marketing_id', request()->input('marketing'));
            });
        }
        if (request()->input('staff')) {
            $query = $query->where(function($q){
                $q->where('user_created', request()->input('staff'))
                ->orWwhere('upsale_from_user_id', request()->input('staff'))
                ->orWwhere('assigned_user_id', request()->input('staff'))
                ->orWwhere('close_user_id', request()->input('staff'))
                ->orWwhere('delivery_user_id', request()->input('staff'))
                ->orWwhere('marketing_id', request()->input('staff'));
            });
        }
        if (request()->input('phone')) {
            $query = $query->whereHas('customer',function($q){
                $q->where('phone','like','%'.request()->input('phone').'%');
            });
        }
        if (request()->input('name')) {
            $query = $query->whereHas('customer',function($q){
                $q->where('name','like','%'.request()->input('name').'%');
            });
        }
        // if (request()->input('product_arr')) {
        //     if (count(request()->input('product_arr'))) {
        //         $query = $query->whereHas('order_products',function($q){
        //             $q->whereIn('product_id',request()->input('product_arr'));
        //         });
        //     }
        // }
        if (request()->input('status_arr')) {
            if (count(request()->input('status_arr'))) {
                $query = $query->whereIn('status_id',request()->input('status_arr'));
            }
        }
        // $date_fields = [
        //     'c' => 'created_at',
        //     's' => 'share_date',
        //     'd' => 'close_date',
        //     't' => 'delivery_date',
        //     'm' => 'collect_money_date'
        // ];
        if (request()->input('c_from') || request()->input('c_to')) {
            if (request()->input('c_from')) {
                $query = $query->where('created_at', '>=', Carbon::createFromFormat('d/m/Y', request()->input('c_from'))->startOfDay());
            }
            if (request()->input('c_to')) {
                $query = $query->where('created_at', '<=', Carbon::createFromFormat('d/m/Y', request()->input('c_to'))->endOfDay());
            }
        }
        if (request()->input('s_from') || request()->input('s_to')) {
            if (request()->input('s_from')) {
                $query = $query->where('share_date', '>=', Carbon::createFromFormat('d/m/Y', request()->input('s_from'))->startOfDay());
            }
            if (request()->input('s_to')) {
                $query = $query->where('share_date', '<=', Carbon::createFromFormat('d/m/Y', request()->input('s_to'))->endOfDay());
            }
        }
        if (request()->input('d_from') || request()->input('d_to')) {
            if (request()->input('d_from')) {
                $query = $query->where('close_date', '>=', Carbon::createFromFormat('d/m/Y', request()->input('d_from'))->startOfDay());
            }
            if (request()->input('d_to')) {
                $query = $query->where('close_date', '<=', Carbon::createFromFormat('d/m/Y', request()->input('d_to'))->endOfDay());
            }
        }
        if (request()->input('t_from') || request()->input('t_to')) {
            if (request()->input('t_from')) {
                $query = $query->where('delivery_date', '>=', Carbon::createFromFormat('d/m/Y', request()->input('t_from'))->startOfDay());
            }
            if (request()->input('t_to')) {
                $query = $query->where('delivery_date', '<=', Carbon::createFromFormat('d/m/Y', request()->input('t_to'))->endOfDay());
            }
        }
        if (request()->input('m_from') || request()->input('m_to')) {
            if (request()->input('m_from')) {
                $query = $query->where('collect_money_date', '>=', Carbon::createFromFormat('d/m/Y', request()->input('m_from'))->startOfDay());
            }
            if (request()->input('m_to')) {
                $query = $query->where('collect_money_date', '<=', Carbon::createFromFormat('d/m/Y', request()->input('m_to'))->endOfDay());
            }
        }
        if (request()->input('returned')) {
            $query = $query->where('returned',request()->input('returned'));
        }
        if (request()->input('month_arr') && count(request()->input('month_arr'))) {
            $ids_order = [];
            $first_order = true;
            foreach(request()->input('month_arr') as $i) {
                $ids = Order::where('shop_id',auth()->user()->shop_id)->whereNull('deleted_at')
                ->where('complete_date','>=',date('Y-m-01',strtotime('-'.(11-$i).' months')))
                ->where('complete_date','<=',date('Y-m-t',strtotime('-'.(11-$i).' months')))
                ->pluck('id')->all();
                if (empty($ids_order)) {
                    $first_order = false;
                    $ids_order = $ids;
                }
                if (!$first_order) {
                    $ids_order = array_intersect($ids_order, $ids);
                }
            }
            $query = $query->whereIn('id', $ids_order);
        }
        $items = $query->paginate(20);
        $conditions = OrderType::get();
        $status_arr = OrderStatus::all();
        $source_arr = OrderSource::all();
        $product_arr = Product::active()->where('shop_id',auth()->user()->shop_id)->get();
        $bundle_arr = ProductBundle::where('shop_id',auth()->user()->shop_id)->get();
        $level_arr = CustomerLevel::all();
        $user_list = User::active()->get();
        return view('admin.customer.overview',compact('items','conditions','status_arr','user_list','source_arr','product_arr','bundle_arr','level_arr'));
    }
    public function overviewTagAdd()
    {
        $customer = Customer::find(request()->input('customer_id'));
        $tags = explode(',', $customer->tags ?: '');
        $value = request()->input('value');
        if (in_array($value, $tags)) {
            $tags = array_filter($tags,function($tag)use($value){return $tag != $value;});
        } else {
            $tags[] = $value;
        }
        $tags = array_values(array_filter($tags));
        Customer::find(request()->input('customer_id'))->update(['tags' => implode(',',$tags)]);
        return response()->json(['success'=>true,'tags'=>$tags,'msg'=>'Cập nhật thành công']);
    }
    public function overviewTagUpdate()
    {
        $customer = Customer::find(request()->input('customer_id'));
        $tags = explode(',', $customer->tags ?: '');
        $value = request()->input('value');
        if (in_array($value, $tags)) {
            $tags = array_filter($tags,function($tag)use($value){return $tag !== $value;});
        } else {
            $tags[] = $value;
        }
        $tags = array_filter($tags);
        $tags = implode(',',ProductBundle::whereIn('name',$tags)->orWhere('id',$tags)->pluck('id')->toArray());
        Customer::find(request()->input('customer_id'))->update(['tags' => implode(',',$tags)]);
        return response()->json(['success'=>true,'msg'=>'Cập nhật thành công']);
    }
    public function overviewUpdate()
    {
        $field = request()->input('field');
        $value = request()->input('value');
        if (in_array($field,['buy_capacity','sale_note','refferal','job','app_installed','medical_condition'])) {
            Customer::find(request()->input('customer_id'))->update([$field => $value]);
            return response()->json(['success'=>true,'msg'=>'Cập nhật thành công']);
        }
        return response()->json(['success'=>false,'msg'=>'Thao tác không được phép']);
    }
    public function index(Request $request)
    {
        $params = $request->all();
        $listCustomer = Customer::orderBy('created_at')
            ->leftJoin('customer_groups', 'customer_groups.id', 'customers.customer_group_id')
            ->leftJoin('users as user_confirm', 'user_confirm.id', 'customers.user_confirm_id')
            ->leftJoin('users as created_by', 'created_by.id', 'customers.created_by')
            ->select('customers.*', 'user_confirm.name as user_confirm_name', 'created_by.name as created_by_name', 'customer_groups.name as customer_groups_name');
        if (isset($params['name'])) {
            $listCustomer = $listCustomer->where(function ($query) use ($params) {
                $query->where('customers.name', 'like', '%' . $params['name'] . '%');
            });
        }
        if (isset($params['phone'])) {
            $listCustomer = $listCustomer->where('customers.phone', $params['phone']);
        }
        if (isset($params['customer_group_id'])) {
            $listCustomer = $listCustomer->where('customers.customer_group_id', $params['customer_group_id']);
        }
        if (isset($params['created_from'])) {
            $create_from = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d');
            $listCustomer = $listCustomer->whereDate('customers.created_at', '>=', $create_from);
        }
        if (isset($params['created_to'])) {
            $created_to = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d');
            $listCustomer = $listCustomer->whereDate('customers.created_at', '<=', $created_to);
        }
        if (isset($params['birth'])) {
            if ($params['birth'] == 'today') {
                $listCustomer = $listCustomer->whereDate('customers.birthday', '=', Carbon::today()->toDateString());
            }
            if ($params['birth'] == 'week') {
                $now = Carbon::now();
                $weekStartDate = $now->startOfWeek()->format('Y-m-d');
                $weekEndDate = $now->endOfWeek()->format('Y-m-d');
                $listCustomer = $listCustomer->whereBetween('customers.birthday', [$weekStartDate, $weekEndDate]);
            }
        }
        if (isset($params['type'])) {
            $listCustomer = $listCustomer->where('customers.type', $params['type']);
        }
        $listCustomer = $listCustomer->where('customers.shop_id', getCurrentUser()->shop_id)->paginate(10);
        // dd($listCustomer);
        $customerGroup = CustomerGroup::where('parent_id', '<>', 0)->pluck('name', 'id')->toArray();
        $customerCareStatus = CUSTOMER_CARE;
        $type = $request->get('method');
        $query = [];
        if ($type == 'select') {
            $query = ['method' => 'select'];
        }
        return view('admin.customer.index', compact('listCustomer', 'customerGroup', 'customerCareStatus', 'query'));
    }
    public function saveCallHistory(Request $request)
    {
        $data = $request->all();
        $rules = [
            'call_content' => 'required',
        ];
        $messages = [
            'call_content.required' => 'Vui lòng nhập vào nội dung cuộc gọi!',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if (!empty($errors)) {
                return response(json_encode(['status' => 'NG', 'message' => array_shift($errors)]), HTTP_STATUS_SUCCESS);
            }
        } else {
            if (isset($data['call_id'])) {
                $callHistory = CallHistory::find($data['call_id']);
                $historyUpdate = unserialize($callHistory->history_update);
                $newUpdate = $this->compareDataUpdate($callHistory, $data, $type = 'call');

                if (!empty($newUpdate)) {
                    $historyUpdate[] = $newUpdate;
                }
                $callHistory->history_update = serialize($historyUpdate);

            } else {
                $data['call_create_by_id'] = Auth::id();
                $callHistory = new CallHistory;
            }
            $callHistory->content = $data['call_content'];
            $callHistory->customer_emotions = $data['call_customer_emotions'];
            $callHistory->date_create = $data['call_date_create'];
            $callHistory->create_by = $data['call_create_by_id'];
            $callHistory->customer_id = $data['call_customer_id'];
            $callHistory->customer_care_id = $data['call_customer_care_id'];
            $callHistory->save();
            return $this->statusOK();
        }
        return $this->statusOK();
    }

    public function historyCall(Request $request)
    {
        $id = $request->customer_id;
        $listCall = CallHistory::orderBy('created_at', 'desc')
            ->selectRaw("date_format(date_create,'%h:%i:%s %d-%m-%Y') as date_create")
            ->where('customer_id', $id)
            ->get();
        return response(json_encode(['status' => "OK", 'data' => $listCall]), HTTP_STATUS_SUCCESS);

    }

    public function saveNoteHistory(Request $request)
    {
        $data = $request->all();
        $rules = [
            'note_content' => 'required',
        ];
        $messages = [
            'note_content.required' => 'Vui lòng nhập vào nội dung ghi chú!',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if (!empty($errors)) {
                return response(json_encode(['status' => 'NG', 'message' => array_shift($errors)]), HTTP_STATUS_SUCCESS);
            }
        } else {
            // dd($data);
            if (isset($data['note_id'])) {
                $noteHistory = NoteHistory::find($data['note_id']);
                $historyUpdate = unserialize($noteHistory->history_update);
                $newUpdate = $this->compareDataUpdate($noteHistory, $data, $type = 'note');

                if (!empty($newUpdate)) {
                    $historyUpdate[] = $newUpdate;
                }
                $noteHistory->history_update = serialize($historyUpdate);

            } else {
                $data['note_create_by_id'] = Auth::id();
                $noteHistory = new NoteHistory;
            }
            $noteHistory->content = $data['note_content'];
            $noteHistory->customer_emotions = $data['note_customer_emotions'];
            $noteHistory->date_create = $data['note_date_create'];
            $noteHistory->create_by = $data['note_create_by_id'];
            $noteHistory->customer_id = $data['note_customer_id'];
            $noteHistory->save();
            return $this->statusOK();
        }
        return $this->statusOK();
    }

    public function compareDataUpdate($oldData, $newData, $type)
    {
        $userChange = Auth::user()->name;
        $changeContent = [];
        if ($type != 'pathological') {
            if ($oldData->content != $newData[$type . '_content']) {
                $changeContent['content']['old'] = trim($oldData->content);
                $changeContent['content']['new'] = trim($newData[$type . '_content']);
            }
            if ($oldData->customer_emotions != $newData[$type . '_customer_emotions']) {
                $changeContent['emotions']['old'] = trim($oldData->customer_emotions);
                $changeContent['emotions']['new'] = trim($newData[$type . '_customer_emotions']);
            }
            if ($type == 'call' && $oldData->customer_care_id != $newData[$type . '_customer_care_id']) {
                $changeContent['customer_care']['old'] = trim($oldData->customer_care_id);
                $changeContent['customer_care']['new'] = trim($newData[$type . '_customer_care_id']);
            }
        }

        if ($type == 'pathological') {
            if ($oldData->name != $newData[$type . '_name']) {
                $changeContent['name']['old'] = trim($oldData->name);
                $changeContent['name']['new'] = trim($newData[$type . '_name']);
            }
            if ($oldData->status != $newData[$type . '_status']) {
                $changeContent['status']['old'] = trim($oldData->status);
                $changeContent['status']['new'] = trim($newData[$type . '_status']);
            }
        }
        return empty($changeContent) ? '' : ['user' => $userChange, 'content' => $changeContent, 'time' => date('Y-m-d H:i:s')];
    }

    public function historyNote(Request $request)
    {
        $id = $request->customer_id;
        $listNote = NoteHistory::selectRaw("date_format(date_create,'%h:%i:%s %d-%m-%Y') as date_create")
            ->where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        return response(json_encode(['status' => "OK", 'data' => $listNote]), HTTP_STATUS_SUCCESS);

    }

    //
    public function customerGroupAdd(Request $request)
    {
        $listParent = CustomerGroup::where('is_default', '<>', ACTIVE)->get();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
            ];
            $messages = [
                'required' => 'Lỗi nhập :attribute ',

            ];
            $fieldNames = [
                'name' => 'tên phân loại khách hàng',
            ];
            $validator = Validator::make($data, $rules, $messages, $fieldNames);
            if ($validator->fails()) {
                // dd($validator->errors());
                return back()->withInput()->withErrors($validator->errors());
            }
            $customerGroup = new CustomerGroup;
            $customerGroup->name = $request->name;
            $customerGroup->parent_id = $request->parent_id;
            $customerGroup->shop_id = getCurrentUser()->shop_id;
            $customerGroup->save();
            return redirect()->route('admin.customer.group.list')->with('success', 'Dữ liệu đã được update');
        }
        return view('admin.customer.create_group', ['listParent' => $listParent]);
    }
    public function customerGroupList(Request $request)
    {
        $customerGroup = CustomerGroup::all();
        $mode = 'list';
        if ($request->isMethod('post')) {
            $data = $request->all();

            if (isset($data['selected_ids']) && !empty($data['selected_ids'])) {
                $customerGroup = [];
                foreach ($data['selected_ids'] as $key => $value) {
                    $customer = CustomerGroup::findOrFail($key);
                    $customerGroup[] = [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'sub' => $value[0],
                    ];
                }
                $mode = 'delete';
            }
        }
        // dd($customerGroup);
        return view('admin.customer.group', ['customerGroup' => $customerGroup, 'mode' => $mode]);
    }

    public function customerGroupDelete(Request $request)
    {
        $data = $request->all();
        if (isset($data['selected_ids']) && !empty($data['selected_ids'])) {
            CustomerGroup::whereIn('id', $data['selected_ids'])->orWhereIn('parent_id', $data['selected_ids'])->delete();
        }
        return redirect()->route('admin.customer.group.list')->with('success', 'Dữ liệu đã được update');
    }
    public function edit($id)
    {
        $info = Customer::findOrFail($id);
        $prefectures = Province::orderBy('_name')->get();
        $sources = OrderSource::orderBy('name', 'desc')->get();
        $users = User::orderBy('name', 'desc')->get();
        $customer_groups = CustomerGroup::where('parent_id', '<>', 0)->orderBy('name', 'desc')->get();
        $customerCareStatus = CUSTOMER_CARE;
        return view('admin.customer.create', compact('prefectures', 'sources', 'users', 'customer_groups', 'info', 'customerCareStatus'));
    }
    public function update(CustomnerRequest $request, $id)
    {
        $info = Customer::findOrFail($id);
        $avatar = $request->file('avatar');
        $params = $request->all();
        $path = 'avatar';
        unset($params['_token'], $params['_method'], $params['avatar'], $params['contact_name']);

        if ($avatar) {
            // Upload ảnh mới
            $avatar = $this->uploadImage($avatar, $path);
            $params['avatar'] = $avatar;
        }
        $params['name'] = htmlspecialchars($request->get('name'));
        DB::beginTransaction();
        try {
            Customer::where('id', $id)->update($params);
            DB::commit();
            // Xóa ảnh cũ
            $old_avatar = $info->avatar;
            if ($old_avatar != null) {
                $this->deleteFile($old_avatar, $path);
            }
            return redirect()->route('admin.customer.index')->with('success', 'Sửa khách hàng thành công');
        } catch (\Exception $e) {
            $this->deleteImageWithPath($avatar);
            // something went wrong
            DB::rollback();
            return back()->with('error', 'Thao tác thất bại');
        }

    }
    public function create()
    {
        $prefectures = Province::orderBy('_name')->get();
        $sources = OrderSource::orderBy('name', 'desc')->get();
        $users = User::orderBy('name', 'desc')->get();
        $customer_groups = CustomerGroup::where('parent_id', '<>', 0)->orderBy('name', 'desc')->get();
        $customerCareStatus = CUSTOMER_CARE;

        return view('admin.customer.create', compact('prefectures', 'sources', 'users', 'customer_groups', 'customerCareStatus'));

    }
    public function store(CustomnerRequest $request)
    {
        $params = $request->all();

        $avatar = $request->file('avatar');
        $upload = null;
        $params['created_by'] = auth()->id();
        $path = 'customer';
        if ($params['birthday'] != null) {
            $params['birthday'] = Carbon::createFromFormat('d/m/Y', $params['birthday'])->format('Y-m-d');
        }
        unset($params['_token'], $params['avatar'], $params['contact_name']);
        if ($avatar) {
            // Upload ảnh mới
            $upload = $this->uploadImage($avatar, $path);
            $params['avatar'] = $upload;
        }
        $params['name'] = htmlspecialchars($request->get('name'));
        DB::beginTransaction();
        try {
            Customer::create($params);
            DB::commit();
            return redirect()->route('admin.customer.index')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $e) {
            // Xóa ảnh vừa upload
            $this->deleteImageWithPath($upload);
            // something went wrong
            DB::rollback();
            return back()->with('error', 'Thao tác thất bại');
        }
    }

    public function delete(Request $request) {
        DB::beginTransaction();
        try {
            $deleteAll = $request->get('delete-all');
            $ids = $request->get('ids');
            if (empty($deleteAll)) {
                Customer::query()->whereIn('id', $ids)->delete();
            } else {
                Customer::query()->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->responseWithErrorMessage("Có lỗi xảy ra, vui lòng thử lại sau");
        }
    }

    public function sltContactUser()
    {
        $listCustomer = Customer::all();
        return view('admin.customer.popup_customer_list', compact('listCustomer'));
    }
    public function getList(Request $request)
    {
        $params = $request->all();
        $paginate = Common::toPagination($params);
        $listCustomer = Customer::orderBy($paginate['sort'], $paginate['order'])
            ->leftJoin('customer_groups', 'customer_groups.id', 'customers.customer_group_id')
            ->leftJoin('users as user_confirm', 'user_confirm.id', 'customers.user_confirm_id')
            ->leftJoin('users as created_by', 'created_by.id', 'customers.created_by')
            ->select('customers.*', 'user_confirm.name as user_confirm_name', 'created_by.name as created_by_name', 'customer_groups.name as customer_groups_name')
            ->with(['noteHistories', 'callHistories', 'orders']);
        if (isset($params['name'])) {
            $listCustomer = $listCustomer->where(function ($query) use ($params) {
                $query->where('customers.name', 'like', '%' . $params['name'] . '%');
            });
        }
        if (isset($params['phone'])) {
            $listCustomer = $listCustomer->where('customers.phone', $params['phone']);
        }
        if (isset($params['customer_group_id'])) {
            $listCustomer = $listCustomer->where('customers.customer_group_id', $params['customer_group_id']);
        }
        if (isset($params['created_from'])) {
            $create_from = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d');
            $listCustomer = $listCustomer->whereDate('customers.created_at', '>=', $create_from);
        }
        if (isset($params['created_to'])) {
            $created_to = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d');
            $listCustomer = $listCustomer->whereDate('customers.created_at', '<=', $created_to);
        }
        if (isset($params['birth'])) {
            if ($params['birth'] == 'today') {
                $listCustomer = $listCustomer->whereDate('customers.birthday', '=', Carbon::today()->toDateString());
            }
            if ($params['birth'] == 'week') {
                $now = Carbon::now();
                $weekStartDate = $now->startOfWeek()->format('Y-m-d');
                $weekEndDate = $now->endOfWeek()->format('Y-m-d');
                $listCustomer = $listCustomer->whereBetween('customers.birthday', [$weekStartDate, $weekEndDate]);
            }
        }
        if (isset($params['type'])) {
            $listCustomer = $listCustomer->where('customers.type', $params['type']);
        }
        $listCustomer = $listCustomer->paginate($paginate['limit']);

        $listCustomer = Common::toJson($listCustomer);

        return $listCustomer;
    }
    public function detail($id)
    {
        $info = Customer::findOrFail($id);
        $listOrder = Order::query()->onlyCurrentShop()->where('customer_id', $id)->get();
        $noteHistory = NoteHistory::where('customer_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
        $callHistory = CallHistory::where('customer_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
        $pathologicalData = Pathological::where('customer_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
        $customerCareStatus = CUSTOMER_CARE;
        return view('admin.customer.detail', compact('info', 'listOrder', 'noteHistory', 'callHistory', 'customerCareStatus', 'pathologicalData'));
    }

    public function detailNote(Request $request)
    {
        $id = $request->note_id;
        $noteData = NoteHistory::orderBy('note_histories.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'note_histories.customer_id')
            ->leftJoin('users', 'users.id', 'note_histories.create_by')
            ->select('note_histories.*', 'customers.name as customers_name', 'users.name as create_by_name')
            ->where('note_histories.id', $id)
            ->first();

        $historyUpdate = unserialize($noteData->history_update);
        if ($historyUpdate) {
            $historyUpdate = array_reverse($historyUpdate);
            $historyUpdate = array_slice($historyUpdate, 0, 10);
        }

        return response(json_encode(['status' => "OK", 'data' => $noteData, 'historyUpdate' => $historyUpdate]), HTTP_STATUS_SUCCESS);
    }

    public function detailCall(Request $request)
    {
        $id = $request->call_id;
        $callData = CallHistory::orderBy('call_histories.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'call_histories.customer_id')
            ->leftJoin('users', 'users.id', 'call_histories.create_by')
            ->select('call_histories.*', 'customers.name as customers_name', 'users.name as create_by_name')
            ->where('call_histories.id', $id)
            ->first();
        // dd($callData);
        $historyUpdate = unserialize($callData->history_update);
        if ($historyUpdate) {
            $historyUpdate = array_reverse($historyUpdate);
            $historyUpdate = array_slice($historyUpdate, 0, 10);
        }
        return response(json_encode(['status' => "OK", 'data' => $callData, 'historyUpdate' => $historyUpdate]), HTTP_STATUS_SUCCESS);
    }

    public function listDetailCall(Request $request, $customerId)
    {
        $params = $request->all();
        $customer = Customer::findOrFail($customerId);
        $callData = CallHistory::orderBy('call_histories.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'call_histories.customer_id')
            ->leftJoin('users', 'users.id', 'call_histories.create_by')
            ->select('call_histories.*', 'customers.name as customers_name', 'users.name as create_by_name', 'customers.phone as phone')
            ->where('call_histories.customer_id', $customerId);
        if (isset($params['keyword'])) {
            $callData = $callData->where(function ($query) use ($params) {
                $query->orWhere('customers_name', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('phone', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('call_histories.id', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('call_histories.content', 'like', '%' . $params['keyword'] . '%');
            });
        }
        if (isset($params['status'])) {
            $callData = $callData->where('call_histories.customer_care_id', $params['status']);
        }
        if (isset($params['created_from'])) {
            $create_from = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d');
            $callData = $callData->whereDate('call_histories.created_at', '>=', $create_from);
        }
        if (isset($params['created_to'])) {
            $created_to = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d');
            $callData = $callData->whereDate('call_histories.created_at', '<=', $created_to);
        }
        $callData = $callData->paginate(10);
        $customerCareStatus = CUSTOMER_CARE;
        return view('admin.customer.list_detail_call', compact('callData', 'customerId', 'customerCareStatus', 'customer'));
    }

    public function listDetailNote(Request $request, $customerId)
    {
        $params = $request->all();
        $customer = Customer::findOrFail($customerId);
        $noteData = NoteHistory::orderBy('note_histories.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'note_histories.customer_id')
            ->leftJoin('users', 'users.id', 'note_histories.create_by')
            ->select('note_histories.*', 'customers.name as customers_name', 'users.name as create_by_name', 'customers.phone as phone')
            ->where('note_histories.customer_id', $customerId);
        if (isset($params['keyword'])) {
            $noteData = $noteData->where(function ($query) use ($params) {
                $query->orWhere('customers_name', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('phone', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('note_histories.id', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('note_histories.content', 'like', '%' . $params['keyword'] . '%');
            });
        }
        if (isset($params['created_from'])) {
            $create_from = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d');
            $noteData = $noteData->whereDate('note_histories.created_at', '>=', $create_from);
        }
        if (isset($params['created_to'])) {
            $created_to = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d');
            $noteData = $noteData->whereDate('note_histories.created_at', '<=', $created_to);
        }
        $noteData = $noteData->paginate(10);
        $customerCareStatus = CUSTOMER_CARE;
        return view('admin.customer.list_detail_note', compact('noteData', 'customerId', 'customerCareStatus', 'customer'));
    }

    public function savePathological(Request $request)
    {
        $data = $request->all();
        $rules = [
            'pathological_name' => 'required',
            'pathological_status' => 'required',
        ];
        $messages = [
            'pathological_name.required' => 'Vui lòng nhập vào tên bệnh lý!',
            'pathological_status.required' => 'Vui lòng nhập vào tình trạng bệnh lý!',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if (!empty($errors)) {
                return response(json_encode(['status' => 'NG', 'message' => array_shift($errors)]), HTTP_STATUS_SUCCESS);
            }
        } else {
            // dd($data);

            // dd($data);
            if (isset($data['pathological_id'])) {
                $pathological = Pathological::find($data['pathological_id']);
                $historyUpdate = unserialize($pathological->history_update);
                $newUpdate = $this->compareDataUpdate($pathological, $data, $type = 'pathological');

                if (!empty($newUpdate)) {
                    $historyUpdate[] = $newUpdate;
                }
                $pathological->history_update = serialize($historyUpdate);
            } else {
                $data['pathological_create_by_id'] = Auth::id();
                $pathological = new Pathological;
            }
            $pathological->name = $data['pathological_name'];
            $pathological->status = $data['pathological_status'];
            $pathological->date_create = $data['pathological_date_create'];
            $pathological->create_by = $data['pathological_create_by_id'];
            $pathological->customer_id = $data['pathological_customer_id'];
            $pathological->save();
            return $this->statusOK();
        }
        return $this->statusOK();
    }

    public function detailPathological(Request $request)
    {
        $id = $request->pathological_id;
        $pathologicalData = Pathological::orderBy('Pathological.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'Pathological.customer_id')
            ->leftJoin('users', 'users.id', 'Pathological.create_by')
            ->select('Pathological.*', 'customers.name as customers_name', 'users.name as create_by_name')
            ->where('Pathological.id', $id)
            ->first();
        // dd($pathologicalData);
        $historyUpdate = unserialize($pathologicalData->history_update);
        if ($historyUpdate) {
            $historyUpdate = array_reverse($historyUpdate);
            $historyUpdate = array_slice($historyUpdate, 0, 10);
        }
        return response(json_encode(['status' => "OK", 'data' => $pathologicalData, 'historyUpdate' => $historyUpdate]), HTTP_STATUS_SUCCESS);
    }
    public function listDetailPathological(Request $request, $customerId)
    {
        $params = $request->all();
        $customer = Customer::findOrFail($customerId);
        $pathologicalData = Pathological::orderBy('pathological.created_at', 'desc')
            ->leftJoin('customers', 'customers.id', 'pathological.customer_id')
            ->leftJoin('users', 'users.id', 'pathological.create_by')
            ->select('pathological.*', 'customers.name as customers_name', 'users.name as create_by_name', 'customers.phone as phone')
            ->where('pathological.customer_id', $customerId);
        if (isset($params['keyword'])) {
            $pathologicalData = $pathologicalData->where(function ($query) use ($params) {
                $query->orWhere('customers_name', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('phone', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('pathological.id', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('pathological.status', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('pathological.name', 'like', '%' . $params['keyword'] . '%');
            });
        }
        if (isset($params['created_from'])) {
            $create_from = Carbon::createFromFormat(config('app.date_format'), $params['created_from'])->format('Y-m-d');
            $pathologicalData = $pathologicalData->whereDate('pathological.date_create', '>=', $create_from);
        }
        if (isset($params['created_to'])) {
            $created_to = Carbon::createFromFormat(config('app.date_format'), $params['created_to'])->format('Y-m-d');
            $pathologicalData = $pathologicalData->whereDate('pathological.date_create', '<=', $created_to);
        }
        $pathologicalData = $pathologicalData->paginate(10);
        $customerCareStatus = CUSTOMER_CARE;
        return view('admin.customer.list_detail_pathological', compact('pathologicalData', 'customerId', 'customerCareStatus', 'customer'));
    }
}
