<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStoreRequest extends FormRequest
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
        return [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'max_user' => 'required',
            'owner_password' => 'confirmed',
            'owner_name' => 'required',
        ];

    }

    public function messages()
    {
        return [
            'name.required' => "Vui lòng nhập tên cửa hàng",
            'address.required' => "Vui lòng nhập địa chỉ cửa hàng",
            'phone.required' => "Vui lòng nhập số điện thoại cửa hàng",
            'max_user.required' => "Vui lòng nhập số nhân viên tối đa",
            'owner_password.confirmed' => "Xác nhận mật khẩu không đúng",
            'owner_name.required' => "Vui lòng tên chủ cửa hàng",
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     $respone = ['error_code' => HTTP_STATUS_BAD_REQUEST, 'message' => $validator->getMessageBag()->first()];
    //     throw new HttpResponseException(response()->json($respone, HTTP_STATUS_BAD_REQUEST));
    // }
}