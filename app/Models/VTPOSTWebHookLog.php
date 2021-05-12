<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VTPOSTWebHookLog extends Model
{
    protected $table = 'vtpost_webhook_logs';

    protected $fillable = [
        'ip', 'order_number', 'order_reference', 'order_statusdate', 'order_status', 'location_currently', 'note', 'product_weight', 'json_body'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'shipping_code', 'order_number');
    }
}
