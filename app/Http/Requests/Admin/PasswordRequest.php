<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
            'old_password' => ['required'],
            'password' => ['required', 'confirmed'],
        ];
    }
    public function messages()
    {

        return [
            'old_password.required' => 'Mật khẩu cũ không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'confirmed' => 'Xác nhận mật khẩu không đúng'
        ];
    }
}
