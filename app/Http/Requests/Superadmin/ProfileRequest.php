<?php

namespace App\Http\Requests\Superadmin;

use App\Rules\MatchOldPassword;
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
        return [
            'password' => ['required', 'confirmed'],
            'password_confirmation' => 'required',
            'old_password' => ['required', new MatchOldPassword],
        ];

    }

    public function messages()
    {
        return [
            'password.required' => "Vui lòng nhập mật khẩu mới",
            'password_confirmation.required' => "Vui lòng nhập mật khẩu xác nhận",
            'old_password.required' => "Vui lòng nhập mật khẩu cũ",
        ];
    }

}