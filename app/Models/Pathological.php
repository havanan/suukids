<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pathological extends Model
{
    protected $table = 'pathological';
    function create_by() {
        return $this->belongsTo(User::class, 'create_by');
    }
}
