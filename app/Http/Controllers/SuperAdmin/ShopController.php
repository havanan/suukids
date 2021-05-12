<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\CreateShopRequest;
use App\Http\Requests\Superadmin\UpdateStoreRequest;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Shop;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::query()->with('owner')->get();
        return view('superadmin.shop.index', compact('shops'));
    }

    public function create()
    {
        return view('superadmin.shop.create');
    }

    public function store(CreateShopRequest $request)
    {
        $shopData = $request->only('name', 'phone', 'address', 'max_user');
        $userData = [
            'account_id' => $request->get('owner_username'),
            'password' => Hash::make($request->get('owner_password')),
            'name' => $request->get('owner_name'),
            'email' => $request->get('owner_email'),
            'phone' => $request->get('owner_phone'),
            'address' => $request->get('owner_address'),
            'shop_manager_flag' => 1,
        ];
        try {
            DB::beginTransaction();

            $shop = Shop::query()->create($shopData);
            $userData['shop_id'] = $shop->id;
            $user = User::query()->create($userData);

            $shop->owner_id = $user->id;
            $shop->save();

            // Thêm quyền mặc định MKT và SALE
            $permission = Permission::findOrFail(1);

            $mkt = $permission->replicate();

            $mkt->shop_id = $shop->id;
            $mkt->save();

            $permission = Permission::findOrFail(2);
            $sale = $permission->replicate();
            $sale->shop_id = $shop->id;
            $sale->save();

            DB::commit();
            return \redirect()->route('superadmin.shop.index')->with('success', 'Tạo cửa hàng thành công');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            dd($e);
            return \redirect()->back()->with('error', 'Tạo cửa hàng thất bại, vui lòng thử lại sau');
        }
    }

    public function edit(Request $request, $id)
    {
        $info = Shop::query()->whereKey($id)->first();
        return view('superadmin.shop.create', compact('info'));
    }

    public function update(UpdateStoreRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $shopData = $request->only('name', 'phone', 'address', 'max_user', 'expired_date', 'is_pause');
            $userData = [
                'name' => $request->get('owner_name'),
                'email' => $request->get('owner_email'),
                'phone' => $request->get('owner_phone'),
                'address' => $request->get('owner_address'),
            ];

            if (!empty($request->get('owner_password'))) {
                $userData['password'] = Hash::make($request->get('owner_password'));
            }
            if (DateTime::createFromFormat('d/m/Y', $shopData['expired_date']) == false) {
                $shopData['expired_date'] = null;
            }
            if (!empty($shopData['expired_date'])) {
                $shopData['expired_date'] = Carbon::createFromFormat("d/m/Y", $shopData['expired_date'])->format("Y-m-d");
            }
            $shop = Shop::findOrFail($id);
            $shop->fill($shopData);
            $shop->save();

            $user = $shop->owner;
            $user->fill($userData);
            $user->save();

            DB::commit();

            return \redirect()->route('superadmin.shop.index')->with('success', 'Cập nhật cửa hàng thành công');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return \redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }

    public function deleteShop(Request $request)
    {
        try {
            $shopId = $request->get('id');
            $shop = Shop::findOrFail($shopId);

            DB::beginTransaction();
            $shop->delete();
            User::where('shop_id', $shopId)->delete();
            Order::where('shop_id', $shopId)->delete();
            DB::commit();
            return $this->statusOK();
        } catch (\Exception $ex) {
            DB::rollback();

            return $this->statusNG();
        }

    }

    public function shopLogin(Request $request, $shopId)
    {
        if (Auth::guard('superadmin')->check()) {
            $user = User::query()->where('shop_id', $shopId)->firstOrFail();
            if ($user) {
                Auth::guard('users')->loginUsingId($user->id, true);
            }
        }
        return redirect(route('login'));
    }
}
