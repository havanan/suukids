<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StockOutRequest extends FormRequest
{
    public $params;
    public $id;

    public function __construct(&$params, $id = null)
    {
        $this->params = $params;
        $this->id = $id;
        $params = $this->prepare();
    }

    private function prepare()
    {
        $this->params['create_day'] = !empty($this->params['create_day']) ? Carbon::createFromFormat(config('app.date_format'), $this->params['create_day'])->format('Y-m-d') : "";
        return $this->params;
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
            'create_day' => 'required|date',
            'bill_number' => [
                'required',
                Rule::unique('stock_out')->ignore($this->id, 'id')
            ],
            'supplier_id' => 'exists:suppliers,id|nullable',
            'total' => 'numeric',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'bill_number.required' => 'Chưa nhập số phiếu.',
            'create_day.required' => 'Chưa nhập ngày tạo.',
            'create_day.date' => 'Định dạng ngày tạo sai.',
            'bill_number.unique' => 'Số phiếu trùng. Kiểm tra lỗi nhập/xuất hàng hóa (Chú ý số lượng tồn kho khi xuất hàng)',
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