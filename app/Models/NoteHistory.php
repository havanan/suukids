<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteHistory extends Model
{
    protected $fillable = ['content','customer_emotions','date_create','customer_id','create_by'];

    public function createBy() {
        return $this->belongsTo(User::class, 'create_by');
    }
}
