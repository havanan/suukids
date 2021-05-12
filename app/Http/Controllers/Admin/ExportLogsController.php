<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExportLog;


class ExportLogsController extends Controller
{

    public function excel(Request $request)
    {
        $logList = ExportLog::where('shop_id', getCurrentUser()->shop_id);

        if (isset($request['search_input'])) {
            $logList = $logList->where(function ($query) use ($request) {
                $query->where('export_excel_logs.title', 'like', '%' . $request['search_input'] . '%')
                    ->orWhere('export_excel_logs.detail', 'like', '%' . $request['search_input'] . '%');
            });
        }

        $logList = $logList->paginate(10);

        return view('admin.export_logs.excel', compact('logList', 'request'));
    }
}
