<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FcmToken extends Model
{
    protected $table = 'fcm_tokens';
    protected $fillable = ['user_id', 'token'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}