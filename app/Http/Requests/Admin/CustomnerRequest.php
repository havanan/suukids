<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomnerRequest extends FormRequest
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
        $id = $this->id;
        // trường hợp edit
        if ($this->_method == "PUT" || $this->_method == "PATCH") {
            $id = $this->id;
            return [
                'name' => 'required|max:512',
                'avatar' => 'image|mimes:jpg,jpeg,gif',
                'customer_group_id' => 'required|max:20',
//                'weight' => 'digits_between:1,11',
                //                'email' =>'email|max:512',
                'phone' => [
                    'required',
                    'max:15',
                    Rule::unique('customers')->ignore($this->id, 'id')->where(function ($query) {
                        return $query->where('customers.shop_id', '=', getCurrentUser()->shop_id);
                    }),
                ],
                'address' => 'max:255',
                'job' => 'max:256',
                'position' => 'max:256',
                'sex' => 'numeric|digits_between:1,4',
//                'prefecture' =>'numeric|digits_between:1,4',
                'source_id' => 'numeric|digits_between:1,4',
//                'user_confirm_id' =>'numeric|digits_between:1,20',
                'note' => 'max:1000',
                'note_alert' => 'max:1000',
                'bank_account_numeric' => 'max:256',
                'bank_account_name' => 'max:512',
                'bank_name' => 'max:512',
            ];
        }
        return [
            'name' => 'required|max:512',
            'avatar' => 'image|mimes:jpg,jpeg,gif',
            'customer_group_id' => 'required|max:20',
//            'weight' => 'digits_between:1,11',
            //            'email' =>'email|max:512',
            'phone' => [
                'required',
                'max:15',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('customers.shop_id', '=', getCurrentUser()->shop_id);
                }),
            ],
            'address' => 'max:255',
            'job' => 'max:256',
            'position' => 'max:256',
            'sex' => 'numeric|digits_between:1,4',
//            'prefecture' =>'numeric|digits_between:1,4',
            'source_id' => 'numeric|digits_between:1,4',
//            'user_confirm_id' =>'numeric|digits_between:1,20',
            'note' => 'max:1000',
            'note_alert' => 'max:1000',
            'bank_account_numeric' => 'max:256',
            'bank_account_name' => 'max:512',
            'bank_name' => 'max:512',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Họ và tên không được để trống',
            'name.max' => 'Họ và tên tối đa :max ký tự',

            'email.required' => 'Email không được để trống',
            'email.max' => 'Email tối đa :max ký tự',
            'email.unique' => 'Email đã tồn tại',
            'email.email' => 'Email không đúng định dạng',

            'address.max' => 'Địa chỉ tối đa :max ký tự',
            'job.max' => 'Màu tối đa :max ký tự',

            'phone.required' => 'Số điện thoại không được để trống',
            'phone.max' => 'Số điện thoại tối đa :max ký tự',
            'phone.unique' => 'Số điện thoại đã tồn tại',

            'avatar.image' => 'Avatar phải là file ảnh',
            'avatar.mimes' => 'File upload thuộc định dạng: :mimes',

            'customer_group_id.required' => 'Nhóm phân loại không được để trống',
        ];
    }
}