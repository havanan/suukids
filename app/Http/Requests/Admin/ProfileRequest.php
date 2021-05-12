<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        $user_id = $this->id;
        return [
            'email' => 'required|email|max:512|unique:admin,email,' . $user_id,
            'name' => 'max:512',
            'phone' => 'required|max:15|unique:admin,phone,' . $user_id,
            'avatar' => 'image|mimes:jpg,jpeg,gif,png',
            'address' => 'max:1000',
            'cmtnd' => 'max:50',
        ];
    }
    public function messages()
    {
        return [

            'email.required' => 'Email không được để trống',
            'email.max' => 'Email tối đa :max ký tự',
            'email.unique' => 'Email đã tồn tại',
            'email.email' => 'Email không đúng định dạng',

            'name.max' => 'Họ và tên tối đa :max ký tự',
            'address.max' => 'Địa chỉ tối đa :max ký tự',
            'color.max' => 'Màu tối đa :max ký tự',

            'phone.max' => 'Số điện thoại tối đa :max ký tự',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'phone.required' => 'Số điện thoại không được để trống',

            'avatar.image' => 'Avatar phải là file ảnh',
            'avatar.mimes' => 'File upload thuộc định dạng: :mimes',

            'cmtnd.max' => 'Số CMTND tối đa :max ký tự',

        ];
    }
}