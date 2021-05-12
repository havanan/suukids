<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public $params;
    public $id;

    public function __construct(&$params)
    {
        $this->params = $params;
        $this->id = $params['id'];
    }

    
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
        $rules = [
            'code' => [
                Rule::unique('suppliers')->ignore($this->id, 'id')
            ],
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'code.unique' => 'Mã khách hàng đã được đăng ký',
        ];
    }

    public function validate()
    {
        $rules = $this->rules();
        $messages = $this->messages();
        $validator = Validator::make($this->params, $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if (!empty($errors)) {
                return array_values($errors);
            }
            return false;
        }
        return true;
    }
}