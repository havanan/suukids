<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordRequest;
use App\Http\Requests\Admin\ProfileRequest;
use App\Http\Requests\Admin\UserEditRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Province;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_groups = UserGroup::all();
        $activeAccount = User::onlyCurrentShop()->where('status',ACTIVE)->where('id',"<>",getCurrentUser()->id)->count('id');
        $inActiveAccount = User::onlyCurrentShop()->where('status',INACTIVE)->where('id',"<>",getCurrentUser()->id)->count('id');
        $permissions = Permission::onlyCurrentShop()->get();
        return view('admin.user.index', compact('permissions','user_groups','activeAccount','inActiveAccount'));
    }

    public function getList(Request $request)
    {
        $params = $request->all();
        $data = $this->getData($params);
        return $data;
    }
    public function getData($params = false)
    {
        $paginate = Common::toPagination($params);
        $data = User::query()->where('users.shop_id', getCurrentUser()->shop_id)->orderBy($paginate['sort'], $paginate['order'])
            ->leftJoin('province', 'province.id', 'users.prefecture')
            ->leftJoin('user_groups', 'user_groups.id', 'users.user_group_id')
            ->leftJoin('admin', 'admin.id', 'users.user_create')
            ->select(
                'users.id', 'users.name', 'users.email', 'users.account_id', 'users.birthday', 'users.sex',
                'users.phone', 'users.address', 'users.prefecture', 'users.user_group_id', 'users.type',
                'users.shop_manager_flag', 'users.color', 'users.user_create', 'users.expried_day', 'users.status',
                'users.created_at', 'users.last_online', 'users.last_ip', 'users.extension', 'users.user_group_id',
                'province._name as prefecture_name', 'user_groups.name as group_name', 'admin.name as user_create'
            );
        if (isset($params['keyword'])) {
            $data = $data->where(function ($query) use ($params) {
                $query->where('users.name', 'like', '%' . $params['keyword'] . '%');
                $query->orWhere('users.account_id', 'like', '%' . $params['keyword'] . '%');
            });
        }
        if (isset($params['permission_type'])) {
            $ids = [$params['permission_type']];
            $data = $data->whereHas('userPermission',function($q)use($ids){
                $q->whereIn('permission_id', $ids);
            });
        }
        if (isset($params['account_group_id'])) {
            $data = $data->where('users.user_group_id', $params['account_group_id']);
        }
        if (isset($params['status'])) {
            $data = $data->where('users.status', $params['status']);
        }
        $data = $data->where('users.id', '<>', getCurrentUser()->id);

        $data = $data->with(['userPermission']);

        $data = $data->paginate($paginate['limit']);

        $data = Common::toJson($data);
        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loginTimeFrom = LOGIN_TIME_FROM;
        $loginTimeTo = LOGIN_TIME_TO;
        $prefectures = Province::orderBy('_name')->get();
        $user_groups = UserGroup::all();
        $permissions = Permission::onlyCurrentShop()->get();

        return view('admin.user.create', compact('prefectures', 'user_groups', 'permissions', 'loginTimeFrom', 'loginTimeTo'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $prefectures = Province::orderBy('_name')->get();
        $data['waiting'] = Order::query()->onlyCurrentShop()->where('status_id', 35)->count();
        $data['done'] = Order::query()->onlyCurrentShop()->where('status_id', 38)->count();
        $data['success'] = Order::query()->onlyCurrentShop()->where('status_id', 40)->count();

        return view('admin.user.profile', compact('prefectures', 'data'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(UserRequest $request)
    {

        $params = $request->all();
        unset($params['_token']);

        $shop = Shop::findOrFail(getCurrentUser()->shop_id);
        if (User::where('shop_id', getCurrentUser()->shop_id)->count() > intval($shop->max_user)) {
            return redirect()->route('admin.user.index')->with('error', 'Số nhân viên đã đạt mức tối đa, bạn không thể tạo thêm.');
        }
        $user = new User();
        $user->account_id = $params['account_id'];
        $user->name = isset($params['name']) ? htmlspecialchars($params['name']) : null;
        $user->email = isset($params['email']) ? $params['email'] : null;
        $user->password = Hash::make($params['password']);
        $user->sex = isset($params['sex']) ? $params['sex'] : null;
        $user->birthday = isset($params['birthday']) ? date('Y-m-d', strtotime($params['birthday'])) : null;
        $user->color = isset($params['color']) ? $params['color'] : null;
        $user->phone = isset($params['phone']) ? $params['phone'] : null;
        $user->address = isset($params['address']) ? $params['address'] : null;
        $user->prefecture = isset($params['prefecture']) ? $params['prefecture'] : null;
        $user->expried_day = isset($params['expried_day']) ? date('Y-m-d', strtotime($params['expried_day'])) : null;
        $user->shop_manager_flag = isset($params['shop_manager_flag']) ? $params['shop_manager_flag'] : INACTIVE;
        $user->user_create = Auth::id();
        $user->extension = isset($params['extension']) ? $params['extension'] : null;
        $user->user_group_id = isset($params['user_group_id']) ? $params['user_group_id'] : null;
        $user->status = isset($params['status']) ? $params['status'] : 0;
        $user->shop_id = getCurrentUser()->shop_id;
        $timeFormat = 'H:i';
        $user->login_time_from = (isset($params['login_time_from']) && Carbon::createFromFormat($timeFormat, $params['login_time_from'])) ? Carbon::createFromFormat($timeFormat, $params['login_time_from'])->toDateTimeString() : null;
        $user->login_time_to = (isset($params['login_time_to']) && Carbon::createFromFormat($timeFormat, $params['login_time_to'])) ? Carbon::createFromFormat($timeFormat, $params['login_time_to'])->toDateTimeString() : null;
        DB::beginTransaction();
        try {
            $user->save();
            if (isset($params['permission'])) {
                $permission = $params['permission'];
                $this->createUserPermission($permission, $user->id);
            }
            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return back()->with('error', 'Thao tác thất bại');
        }

    }
    public function createUserPermission($permission, $user_id)
    {
        $result = [];
        $input = [];
        if (empty($permission)) {
            return $result;
        }
        foreach ($permission as $key => $item) {
            $input[$key] = [
                'user_id' => $user_id,
                'permission_id' => $item,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
        }
        if (!empty($input)) {
            $result = UserPermission::insert($input);
        }

        return $result;
    }
    /**
     * update password
     */
    public function updatePass(PasswordRequest $request)
    {
        $old_password = $request->get('old_password');
        $password = $request->get('old_password');
        $user_id = Auth::id();
        $info = User::findOrFail($user_id);
        if (Hash::check($old_password, $info['password'])) {
            $info->password = bcrypt($request->get('password'));
            if ($info->save()) {
                return back()->with('success', 'Mật khẩu đã được update');
            } else {
                return back()->with('error', 'Đổi mật khẩu thất bại');
            }
        }
        return back()->with('error', 'Mật khẩu hiện tại không đúng');
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        $id = Auth::user()->id;
        $info = User::findOrFail($id);
        $avatar = $request->file('avatar');
        $params = $request->all();
        $path = 'avatar';
//        dd($params);
        unset($params['_token'], $params['avatar']);
        if ($avatar) {
            // Upload ảnh mới
            $params['avatar'] = $this->uploadImage($avatar, $path);
            // Xóa ảnh cũ
            $old_avatar = $info->avatar;
            $this->deleteImageWithPath($old_avatar);
        }
        $params['birthday'] = date('Y-m-d', strtotime($request->get('birthday')));
        $params['name'] = htmlspecialchars($request->get('name'));
        $update = User::where('id', $id)->update($params);
        $shopUpdate = User::where('shop_id', '=', getCurrentUser()->shop_id)->update([
            'shipping_partner' => $params['shipping_partner']
        ]);
        $shopInfoUpdate = true;
        if (auth()->user()->isAdmin()) {
            $shopInfoUpdate = Shop::query()->whereKey(getCurrentUser()->shop_id)->update([
                'shipping' => $params['shipping_partner']
            ]);
        }

        if ($update && $shopUpdate && $shopInfoUpdate) {
            return back()->with('success', 'Dữ liệu đã được update');
        } else {
            return back()->with('error', 'Update dữ liệu thất bại');
        }

    }
    public function updateMember(UserEditRequest $request, $id)
    {
        $params = $request->all();
        $info = User::findOrFail($id);
        unset($params['_token'], $params['permission'], $params['password']);
        // Mã hóa mk
        $password = $request->get('password');
        if ($password != null) {
            $params['password'] = bcrypt($password);
        }
        if (isset($params['birthday']) && $params['birthday'] != null) {
            $params['birthday'] = Carbon::createFromFormat('d/m/Y', $params['birthday'])->format('Y-m-d');
        }
        if (isset($params['expried_day']) && $params['expried_day'] != null) {
            $params['expried_day'] = Carbon::createFromFormat('d/m/Y', $params['expried_day'])->format('Y-m-d');
        }
        if (!isset($params['status'])) {
            $params['status'] = 0;
        }
        if (!isset($params['shop_manager_flag'])) {
            $params['shop_manager_flag'] = 0;
        }
        $params['name'] = htmlspecialchars($request->get('name'));
        $timeFormat = 'H:i';
        $params['login_time_from'] = (isset($params['login_time_from']) && Carbon::createFromFormat($timeFormat, $params['login_time_from'])) ? Carbon::createFromFormat($timeFormat, $params['login_time_from'])->toDateTimeString() : null;
        $params['login_time_to'] = (isset($params['login_time_to']) && Carbon::createFromFormat($timeFormat, $params['login_time_to'])) ? Carbon::createFromFormat($timeFormat, $params['login_time_to'])->toDateTimeString() : null;
        DB::beginTransaction();
        try {
            // Update info
            User::where('id', $id)->update($params);

            $permission = $request->get('permission');
            // Xóa user permission cũ
            UserPermission::where('user_id', $id)->delete();
            // Tạo user permission
            $this->createUserPermission($permission, $id);
            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'Cập nhật người dùng thành công');
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return back()->with('error', 'Thao tác thất bại');
        }

    }
    public function edit($id)
    {
        $info = User::findOrFail($id);
        $prefectures = Province::orderBy('_name')->get();
        $user_groups = UserGroup::all();
        $permissions = Permission::onlyCurrentShop()->get();
        $loginTimeFrom = isset($info->login_time_from) ? (new Carbon($info->login_time_from))->format('H:i') : null;
        $loginTimeTo = isset($info->login_time_to) ? (new Carbon($info->login_time_to))->format('H:i') : null;

        $user_permissions = UserPermission::where('user_id', $id)->pluck('permission_id');
        if (!empty($user_permissions)) {
            $user_permissions = $user_permissions->toArray();
        } else {
            $user_permissions = [];
        }
        return view('admin.user.create', compact('prefectures', 'user_groups', 'permissions', 'info', 'user_permissions', 'loginTimeFrom', 'loginTimeTo'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $id = request()->get('id');
        $info = User::findOrFail($id);
//        dd($info);
        //        return back();
        if ($info->delete()) {
            return back()->with('success', 'Dữ liệu đã được xóa');
        } else {
            return back()->with('error', 'Xóa dữ liệu thất bại');
        }
    }
}
