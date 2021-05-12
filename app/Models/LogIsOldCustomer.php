<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogIsOldCustomer extends Model
{
    protected $table = 'log_is_old_customer';

    protected $fillable = ['order_id','phone','customer_id','controller','function', 'reason'];

    public function createBy() {
        return $this->belongsTo(User::class, 'create_by');
    }
}