<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VTPOSTService extends Model
{
    protected $table = 'vtpost_services';
    protected $fillable = [
        'service_code',
        'name'
    ];
}
