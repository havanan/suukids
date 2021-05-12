<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'account_id' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'account_id.required' => "Vui lòng nhập tên tài khoản",
            'password.required' => "Vui lòng nhập mật khẩu",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $respone = ['error_code' => HTTP_STATUS_BAD_REQUEST, 'message' => $validator->getMessageBag()->first()];
        throw new HttpResponseException(response()->json($respone, HTTP_STATUS_BAD_REQUEST));
    }
}