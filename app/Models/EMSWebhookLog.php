<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EMSWebhookLog extends Model
{

    protected $table = 'ems_webhook_log';
    protected $fillable = ['ip', 'tracking_code', 'order_code',
                            'status_code', 'status_name', 'note',
                            'locate', 'datetime', 'total_weight', 'json_body', 'ems_transaction'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'shipping_code', 'tracking_code');
    }
}
