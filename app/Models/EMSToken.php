<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EMSToken extends Model
{
    protected $table = 'ems_tokens';
    protected $fillable = [
        'token', 'shop_id'
    ];
}
