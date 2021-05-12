<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
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
            'email' => 'nullable|email|max:512|unique:users,email,' . $this->id,
            'password' => 'max:255',
            'name' => 'required|max:512',
            'avatar' => 'image|mimes:jpg,jpeg,gif',
            'phone' => 'nullable|max:15',
            'address' => 'max:1000',
            'color' => 'max:10',
        ];
    }
    public function messages()
    {
        $today = Carbon::now()->format('d/m/Y');
        return [
            'email.required' => 'Email không được để trống',
            'email.max' => 'Email tối đa :max ký tự',
            'email.unique' => 'Email đã tồn tại',
            'email.email' => 'Email không đúng định dạng',

            'password.max' => 'Mật khẩu tối đa :max ký tự',

            'name.required' => 'Họ và tên không được để trống',
            'name.max' => 'Họ và tên tối đa :max ký tự',
            'address.max' => 'Địa chỉ tối đa :max ký tự',
            'color.max' => 'Màu tối đa :max ký tự',

            'phone.max' => 'Số điện thoại tối đa :max ký tự',
            // 'phone.unique' => 'Số điện thoại đã tồn tại',

            'avatar.image' => 'Avatar phải là file ảnh',
            'avatar.mimes' => 'File upload thuộc định dạng: :mimes',
        ];
    }
}