<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OrderHistory extends Model
{
    protected $table = 'order_histories';
    protected $fillable = ['order_id', 'type', 'message', 'created_by'];

    public function userCreated() {
        return $this->belongsTo(User::class, 'created_by');
    }
}


