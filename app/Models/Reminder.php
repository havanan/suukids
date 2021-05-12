<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'reminders';
    protected $fillable = ['order_id', 'content', 'time','created_by','is_completed'];
}
