<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActionLog;
use App\Models\Shop;
use Carbon\Carbon;

class ActionLogController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('superadmin.action_log.index',compact('shops'));
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
        $data = ActionLog::query()->orderBy($paginate['sort'], $paginate['order']);
        if (isset($params['user_name'])) {
            $data = $data->where(function ($query) use ($params) {
                $query->where('user_name', 'like', '%' . $params['user_name'] . '%');
            });
        }

        if (isset($params['query_content'])) {
            $data = $data->where(function ($query) use ($params) {
                $query->where('content_query', 'like', '%' . $params['query_content'] . '%');
                $query->orWhere('url', 'like', '%' . $params['query_content'] . '%');
            });
        }
        if (!empty($params['shop_id'])) {
            $data = $data->where('shop_id', $params['shop_id']);
        }
        if (isset($params['source'])) {
            if ($params['source'] == '1')
                $data = $data->where('url','like', '%admin/sell/order/create?close_when_done=1%');
            if ($params['source'] == '2')
                $data = $data->where('url', 'admin/sell/order');
        }
        if (isset($params['ip'])) {
            if ($params['ip'] == '1')
                $data = $data->whereIn('ip',['42.113.205.153','27.67.16.251','42.118.38.142']);
            if ($params['ip'] == '2')
                $data = $data->whereNotIn('ip', ['42.113.205.153','27.67.16.251','42.118.38.142']);
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
