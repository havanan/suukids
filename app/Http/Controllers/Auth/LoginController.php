<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\Shop;
use App\Models\LoginLog;
use Illuminate\Support\Facades\URL;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    public function loginForm()
    {
        if (Auth::guard('users')->check()) {
            return redirect(route('admin.sell.order.index'));
        }
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        try {
            $remember = $request->get('remember');
            $credentials = [
                'account_id' => $request->get('account_id'),
                'password' => $request->get('password'),
            ];
            if (Auth::guard('users')->attempt($credentials, $remember)) {
                $shop = Shop::findOrFail(auth()->user()->shop_id);
                if (empty(auth()->user()->status)) {
                    return response()->json([
                        "code" => HTTP_STATUS_BAD_REQUEST,
                        "message" => "Tài khoản của bạn đã bị khóa, vui lòng liên hệ với quản lý",
                    ], HTTP_STATUS_BAD_REQUEST);
                }
                if ($shop->is_pause || ($shop->expired_date && strtotime($shop->expired_date) <= strtotime("now"))) {
                    return response()->json([
                        "code" => HTTP_STATUS_BAD_REQUEST,
                        "message" => "Tài khoản hoặc mật khẩu không đúng",
                    ], HTTP_STATUS_BAD_REQUEST);
                }

                $loginLog = new LoginLog();
                $loginLog->user_id         = getCurrentUser()->id;
                $loginLog->user_name       = getCurrentUser()->name;
                $loginLog->shop_id         = getCurrentUser()->shop_id;
                $loginLog->shop_name       = $shop->name;
                $loginLog->url             = URL::full();
                $loginLog->ip              = request()->ip();
                $loginLog->content_query   = json_encode(
                    [
                    ],JSON_UNESCAPED_UNICODE);
                $loginLog->save();

                return response()->json([
                    "code" => HTTP_STATUS_SUCCESS,
                    "message" => "Đăng nhập thành công",
                    "url" => route('admin.sell.order.index'),
                ], HTTP_STATUS_SUCCESS);
            }
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => "Tài khoản hoặc mật khẩu không đúng",
            ], HTTP_STATUS_BAD_REQUEST);
        } catch (\Exception $exception) {
            return response()->json([
                "code" => HTTP_STATUS_BAD_REQUEST,
                "message" => $exception->getMessage(),
            ], HTTP_STATUS_BAD_REQUEST);
        }
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        auth('users')->logout();
        return redirect('/');
    }

}
