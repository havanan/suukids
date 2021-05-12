<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderStatus;
use App\Http\Controllers\Controller;

class OrderStatusController extends Controller
{
    public function update($id){
        if (request()->input('name')) {
            OrderStatus::find($id)->update(['name'=>request()->input('name')]);
        }
        return response()->json(['success'=>true,'msg'=>'Cập nhật thành công']);
    }

}
