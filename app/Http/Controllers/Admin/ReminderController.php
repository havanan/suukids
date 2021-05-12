<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reminder;
use App\Http\Controllers\Controller;

class ReminderController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $items = Reminder::latest()->paginate(20);
        } else {
            $items = Reminder::latest()->where('created_by',auth()->user()->id)->paginate(20);
        }
        return view('admin.reminder.index',compact('items'));
    }
}
