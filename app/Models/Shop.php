<?php

namespace App\Models;

use App\Models\Traits\CurrentShopScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Shop extends Model
{
    use SoftDeletes;
    use CurrentShopScope;
    use Sortable;
    protected $table = 'shops';
    protected $fillable = ['name', 'phone', 'address', 'max_user', 'owner_id', 'expired_date', 'is_pause', 'settings','shipping'];
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
