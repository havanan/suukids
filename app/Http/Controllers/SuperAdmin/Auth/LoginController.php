<?php

namespace App\Http\Controllers\SuperAdmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        if (Auth::guard('superadmin')->check()) {
            return redirect(route('superadmin.shop.index'));
        }

        return view('superadmin.auth.login');
    }

    public function postLogin(LoginRequest $request) {
        try {
            $remember = $request->get('remember');
            $credentials = [
                'account_id' => $request->get('account_id'),
                'password' => $request->get('password'),
            ];

            if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
                return response()->json([
                    "code" => HTTP_STATUS_SUCCESS,
                    "message" => "Đăng nhập thành công",
                    "url" => route('superadmin.shop.index'),
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

    public function logout()
    {
        auth('superadmin')->logout();
        return redirect('/');
    }
}
