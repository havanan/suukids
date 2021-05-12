<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\ProfileRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function changePassword()
    {
        return view('superadmin.profile.change_password');
    }

    public function changePasswordPost(ProfileRequest $request)
    {
        Admin::findOrFail(auth()->user()->id)->update(['password' => Hash::make($request->get('password'))]);
        return view('superadmin.profile.change_password')->with('messageSuccess', 'Thay đổi mật khẩu thành công!');
    }
}