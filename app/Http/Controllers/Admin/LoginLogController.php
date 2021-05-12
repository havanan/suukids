<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\Shop;
use Carbon\Carbon;

class LoginLogController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('admin.login_log.index',compact('shops'));
    }

    public function getList(Request $request)
    {
        $params = $request->all();
        $data = $this->getData($params);
        return $data;
    }
    public function getData($params = [])
    {
        $paginate = Common::toPagination($params);
        $data = LoginLog::query()->orderBy($paginate['sort'], $paginate['order']);
        if (isset($params['user_name'])) {
            $data = $data->where(function ($query) use ($params) {
                $query->where('user_name', 'like', '%' . $params['user_name'] . '%');
            });
        }
        $data = $data->where('shop_id', auth()->user()->shop_id);
        if (isset($params['query_content'])) {
            $data = $data->where(function ($query) use ($params) {
                $query->where('content_query', 'like', '%' . $params['query_content'] . '%');
                $query->orWhere('url', 'like', '%' . $params['query_content'] . '%');
                $query->orWhere('ip', 'like', '%' . $params['query_content'] . '%');
            });
        }

        $startDate = !empty($params['start_date']) ? Carbon::createFromFormat('d/m/Y', $params['start_date'])->startOfDay() : "";
        $endDate = !empty($params['end_date']) ? Carbon::createFromFormat('d/m/Y', $params['end_date'])->endOfDay() : "";

        if($startDate != "" || $endDate != ""){
            $data = $data->where(function ($query) use ($startDate, $endDate) {
                if($startDate != ""){
                    $query->where('created_at', '>=', $startDate);
                }
                if($endDate != ""){
                    $query->where('created_at', '<=', $endDate);
                }
            });
        }

        $data = $data->paginate($paginate['limit']);
        $data = Common::toJson($data);
        return $data;
    }
}
