<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CurrentShopScope;
use App\Models\Scopes\ShopScope;

//Bảng này gọi là nhóm quyền thì đúng hơn
class Permission extends Model
{
    use CurrentShopScope;

    protected $table = 'permissions';

    protected $fillable = [
        'id', 'name', 'sale_flag', 'customer_manager_flag',
        'view_revenue_sale_flag', 'share_orders_flag', 'bung_don_flag', 'bung_don2_flag', 'bung_don_3_flag',
        'customer_care_flag', 'marketing_flag', 'marketing_manager_flag', 'view_revenue_marketing_flag',
        'export_excel_flag', 'accountant_flag',
        'stock_out_flag', 'stock_manager_flag', 'status_permissions', 'shop_id', 'disable_edit_upsale_flag'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ShopScope);
    }

    //Tên các quyền trong nhóm quyền
    public function getPermissionsTitleAttribute() {
        $permissionsKey = [
            'sale_flag' => $this->sale_flag,
            'customer_manager_flag' => $this->customer_manager_flag,
            'view_revenue_sale_flag' => $this->view_revenue_sale_flag,
            'share_orders_flag' => $this->share_orders_flag,
            'bung_don_flag' => $this->bung_don_flag,
            'bung_don2_flag' => $this->bung_don2_flag,
            'bung_don_3_flag' => $this->bung_don_3_flag,
            'customer_care_flag' => $this->customer_care_flag,
            'marketing_flag' => $this->marketing_flag,
            'marketing_manager_flag' => $this->marketing_manager_flag,
            'view_revenue_marketing_flag' => $this->view_revenue_marketing_flag,
            'export_excel_flag' => $this->export_excel_flag,
            'accountant_flag' => $this->accountant_flag,
            'stock_out_flag' => $this->stock_out_flag,
            'stock_manager_flag' => $this->stock_manager_flag,
            'disable_edit_upsale_flag' => $this->disable_edit_upsale_flag

        ];


        $showPermissions = array_filter($permissionsKey, function ($item) {
           return $item == 1;
        });

        $titles = array_map(function ($key) {
            return PERMISSIONS_TITLE[$key];
        },array_keys($showPermissions));

        return join(" , ", $titles);
    }

    public function getStatusPermissionsInfoAttribute()
    {
        $json = json_decode($this->status_permissions, true);
        if (empty($json)) {
            return [];
        }

        $keys = array_keys($json);
        $statusArray = OrderStatus::query()->currentShop()->whereIn('id', $keys)->get();

        return $statusArray->map(function ($status) use ($json) {
            return [
                'color' => $status->color ? $status->color : "#000000",
                'name' => $status->name,
                'permission' => ORDER_STATUS_PERMISSIONS_TITLE[$json[$status->id]],
                'permission_color' => ORDER_STATUS_PERMISSIONS_COLOR[$json[$status->id]]
            ];
        });
    }
}
