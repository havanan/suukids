<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallHistory extends Model
{
    protected $fillable = ['customer_care_id','content','customer_emotions','date_create','customer_id'];
    
}
