<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
                'code' => [
                    'required',
                    'max:512',
                    Rule::unique('products')->where(function ($query) use($id) {
                        return $query->where('id','<>', $id)->where('shop_id',getCurrentUser()->shop_id);
                    }),
                ],
                'name' => 'required|max:512',
                'price' => 'required',
                'cost_price' => 'required',
                'on_hand' => 'required|max:11',
                'product_image' => 'image|mimes:jpg,jpeg,gif,png',
                'unit_id' => 'max:11',
                'bundle_id' => 'max:11',
                'color' => 'max:10',
                'size' => 'max:256',
                'status' => 'max:4',
            ];
        }

        // add
        return [
            'code' => [
                'required',
                'max:512',
                Rule::unique('products')->where(function ($query) use($id) {
                    return $query->where('shop_id',getCurrentUser()->shop_id);
                }),
            ],
            'name' => 'required|max:512',
            'price' => 'required',
            'cost_price' => 'required',
            'on_hand' => 'required|max:11',
            'product_image' => 'image|mimes:jpg,jpeg,gif,png',
            'unit_id' => 'max:11',
            'bundle_id' => 'max:11',
            'color' => 'max:10',
            'size' => 'max:256',
            'status' => 'max:4',
        ];
    }
    public function messages()
    {
        return [
            'code.required' => 'Mã sản phẩm không được để trống',
            'code.max' => 'Mã sản phẩm tối đa :max ký tự',
            'code.unique' => 'Mã sản phẩm đã tồn tại',

            'name.required' => 'Tên sản phẩm không được để trống',
            'name.max' => 'Tên sản phẩm tối đa :max ký tự',

            'price.required' => 'Giá sản phẩm không được để trống',
            'cost_price.required' => 'Giá nhập không được để trống',
            'on_hand.required' => 'Tồn kho đầu kỳ không được để trống',
            'on_hand.on_hand' => 'Tồn kho đầu kỳ tối đa :max ký tự',

            'color.max' => 'Màu tối đa :max ký tự',
            'bundle_id.max' => 'Loại sản phẩm tối đa :max ký tự',
            'unit_id.max' => 'Đơn vị sản phẩm tối đa :max ký tự',
            'status.max' => 'Tình trạng tối đa :max ký tự',

            'product_image.image' => 'File upload phải là file ảnh',
            'product_image.mimes' => 'File upload phải thuộc định dạng: :mimes',
        ];
    }
}
