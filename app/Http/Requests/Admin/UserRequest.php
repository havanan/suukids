<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today = Carbon::now()->format('Y-m-d');
        return [
            'account_id' => 'required|max:256|unique:users',
            'email' => 'nullable|email|max:512|unique:users',
            'password' => 'required|max:255',
//            'birthday' => 'date|date_format:Y-m-d',
            //            'expried_day' => 'date|date_format:Y-m-d|after_or_equal:'.$today,
            'expried_day' => 'date|nullable',
            'name' => 'required|max:512',
            'avatar' => 'image|mimes:jpg,jpeg,gif',
            'phone' => 'nullable|max:15',
            'address' => 'max:1000',
            'color' => 'max:10',
            'login_time_from' => 'date_format:H:i',
            'login_time_to' => 'date_format:H:i|after:login_time_from',
        ];
    }
    public function messages()
    {
        $today = Carbon::now()->format('d/m/Y');
        return [
            'account_id.required' => 'Tên tài khoản không được để trống',
            'account_id.max' => 'Tên tài khoản tối đa :max ký tự',
            'account_id.unique' => 'Tên tài khoản đã tồn tại',

            'email.required' => 'Email không được để trống',
            'email.max' => 'Email tối đa :max ký tự',
            'email.unique' => 'Email đã tồn tại',
            'email.email' => 'Email không đúng định dạng',

            'password.required' => 'Mật khẩu không được để trống',
            'password.max' => 'Mật khẩu tối đa :max ký tự',

            'birthday.date' => 'Ngày sinh không đúng định dạng',
            'birthday.date_format' => 'Ngày sinh là định dạng Y-m-d',

            'expried_day.date' => 'Ngày hết hạn không đúng định dạng',
            'expried_day.date_format' => 'Ngày hết hạn là định dạng Y-m-d',
            'expried_day.after_or_equal' => 'Ngày hết hạn lớn hơn hoặc bằng hôm nay: ' . $today,

            'name.required' => 'Họ và tên không được để trống',
            'name.max' => 'Họ và tên tối đa :max ký tự',
            'address.max' => 'Địa chỉ tối đa :max ký tự',
            'color.max' => 'Màu tối đa :max ký tự',

            'phone.max' => 'Số điện thoại tối đa :max ký tự',
            // 'phone.unique' => 'Số điện thoại đã tồn tại',

            'avatar.image' => 'Avatar phải là file ảnh',
            'avatar.mimes' => 'File upload thuộc định dạng: :mimes',

            //Login time
            'login_time_from.date_format' => 'Thời gian đăng nhập bắt đầu định dạng không đúng',
            'login_time_to.date_format' => 'Thời gian đăng nhập kết thúc định dạng không đúng',
        ];
    }
}