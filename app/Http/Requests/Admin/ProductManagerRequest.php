<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProductManagerRequest extends FormRequest
{
    public $params;
    public $id;

    public function __construct(&$params, $id = null)
    {
        $this->params = $params;
        $this->id = $id;
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
                'required',
                Rule::unique('products')->ignore($this->id, 'id')
            ],
            'name' => 'required|max:512',
            'price' => 'required',
            'cost_price' => 'required',
            'unit_id' => 'exists:product_units,id|nullable',
            'bundle_id' => 'exists:product_bundles,id|nullable',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Chưa nhập tên sản phẩm.',
            'name.max' => 'Tên sản phẩm tối đa :max ký tự',
            'code.required' => 'Chưa nhập mã sản phẩm.',
            'code.max' => 'Mã sản phẩm tối đa :max ký tự',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'price.required' => 'Giá sản phẩm không được để trống',
            'cost_price.required' => 'Giá nhập không được để trống',
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