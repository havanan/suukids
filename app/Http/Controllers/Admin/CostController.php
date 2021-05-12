<?php

namespace App\Http\Controllers\Admin;

use App\Models\MarketingCost;
use App\Models\OrderSource;
use App\Models\User;
use App\Http\Controllers\Controller;

class CostController extends Controller
{
    public function index()
    {
        $query = MarketingCost::with(['source','created_info'])->latest();
        if (request()->input('source_id')) {
            $query = $query->where('source_id',request()->input('source_id'));
        }
        if (request()->input('user_id')) {
            $query = $query->where('user_id',request()->input('user_id'));
        }
        if (request()->input('type')) {
            $query = $query->where('type',request()->input('type'));
        }
        $date_begin = request()->input('date_begin') ?: date('01/m/Y');
        $date_end = request()->input('date_end') ?: date('d/m/Y');
        $date_begin = \Carbon\Carbon::createFromFormat('d/m/Y',$date_begin)->startOfDay();
        $date_end = \Carbon\Carbon::createFromFormat('d/m/Y',$date_end)->endOfDay();
        $query = $query->whereBetween('day',[$date_begin,$date_end]);
        if (auth()->user()->isAdmin()) {
            $items = $query->paginate(20);
        } else {
            $items = $query->where('user_id',auth()->user()->id)->paginate(20);
        }
        $sources = OrderSource::all();
        $users = User::all();
        return view('admin.cost.index',compact('items','sources','users'));
    }
}
