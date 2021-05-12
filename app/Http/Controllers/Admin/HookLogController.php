<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VTPOSTWebHookLog;

class HookLogController extends Controller {

    public function index() {
        $items = VTPOSTWebHookLog::latest()->paginate(200);
        return view('admin/hook_log/index',compact('items'));
    }
}
