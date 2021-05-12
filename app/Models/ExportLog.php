<?php

namespace App\Models;

use App\Models\Scopes\ShopScope;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Traits\CurrentShopScope;

class ExportLog extends Model
{
    use CurrentShopScope;

    protected $table = 'export_excel_logs';
    protected $fillable = ['id', 'title', 'detail', 'url', 'account_id', 'user_name', 'ip', 'shop_id', 'shop_name', 'created_at', 'updated_at'];
}
