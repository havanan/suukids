<?php

namespace App\Models;

use App\Models\Permission;
use App\Models\Traits\CurrentShopScope;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use CurrentShopScope;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'account_id', 'birthday',
        'sex', 'phone', 'address', 'prefecture',
        'user_group_id', 'expried_day', 'type', 'status',
        'password', 'shop_manager_flag', 'color',
        'user_create', 'last_ip', 'last_online','labor',
        'extension', 'shop_id', 'login_time_from', 'login_time_to',
        'cloudfone_code', 'active_cloudfone', 'mkt_cost','init_cost','bonus_percent','bonus',
        'shipping_partner'
    ];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new ShopScope);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function marketer_members() {
        $group = \App\Models\UserGroup::where('admin_user_id', $this->id)->first();
        if ($group) {
            return \App\Models\User::whereHas('permissions', function ($query) {
                $query->where('marketing_flag', 1);
            })->active()->where('user_group_id', $group->id)->get();
        }
        return collect([]);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    //Check user có quyền với route tương ứng ko
    //Permission ở đây được gắn trong middleware, không phải permission trong database
    //Permission trong database là role thì đúng hơn
    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $keys = array_keys(PERMISSION_CONFIG);

        $avaiableRoleKeys = array_filter($keys, function ($key) use ($permission) {
            return in_array($permission, PERMISSION_CONFIG[$key]);
        });

        $roles = $this->permissions->toArray();

        foreach ($roles as $role) {
            foreach ($avaiableRoleKeys as $roleKey) {
                if (!empty($role[$roleKey])) {
                    return true;
                }
            }
        };

        return false;
    }

    public function hasOnePermissionIn($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function isStockManager()
    {
        if ($this->isAdmin()) {
            return true;
        }

        $roles = $this->permissions;

        foreach ($roles as $role) {
            if ($role->stock_manager_flag) {
                return true;
            }
        }

        return false;
    }

    public function userPermission()
    {
        return $this->hasMany(UserPermission::class, 'user_id')
            ->leftJoin('permissions', 'permissions.id', 'user_permissions.permission_id')
            ->leftJoin('admin', 'admin.id', 'user_permissions.created_by')
            ->select('permissions.name', 'admin.name as created_by', 'permissions.sale_flag as sale_flag', 'permissions.marketing_flag as marketing_flag', 'user_permissions.permission_id', 'user_permissions.user_id');
    }

    public function isAdmin()
    {
        return $this->type === 0 || !empty($this->shop_manager_flag);
    }

    public function isSuperAdmin()
    {
        return false;
    }

    public function isSale()
    {
        $permissions = $this->permissions;
        if (empty($permissions)) {return false;}

        foreach ($permissions as $permission) {
            if ($permission->sale_flag) {
                return true;
            }
        }

        return false;
    }

    public function isMarketing()
    {
        $permissions = $this->permissions;
        if (empty($permissions)) {return false;}

        foreach ($permissions as $permission) {
            if ($permission->marketing_flag) {
                return true;
            }
        }
    }

    public function isOnlyMarketing()
    {
        return $this->isMarketing() && !$this->isSale() && !$this->isAdmin();
    }

    public function isOnlySale()
    {
        return $this->isSale() && !$this->isMarketing() && !$this->isAdmin();
    }

    public function canViewAllOrder()
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->permissions;
        if (empty($permissions)) {return false;}

        foreach ($permissions as $permission) {
            if ($permission->marketing_manager_flag) {
                return true;
            }
        }
    }

    public function canUpdatStatus($status_id)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->permissions;
        foreach ($permissions as $permission) {
            $statuses = $permission->status_permissions;
            if (!empty($statuses)) {
                $data = json_decode($statuses, true);
                if (!empty($data) && !empty($data[$status_id]) && $data[$status_id] == 2) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getViewEnableStatusIds()
    {
        $permissions = $this->permissions;

        $statusIds = [];
        foreach ($permissions as $permission) {
            $status_permissions = $permission->status_permissions;
            if (empty($status_permissions)) {
                continue;
            }

            $statuses = \json_decode($status_permissions);

            foreach ($statuses as $statusId => $value) {
                if ($value) {
                    array_push($statusIds, $statusId);
                }
            }
        }

        return $statusIds;
    }

    public function getEditableStatusIds()
    {
        $permissions = $this->permissions;

        $editableIds = [];
        foreach ($permissions as $permission) {
            $status_permissions = $permission->status_permissions;
            if (empty($status_permissions)) {
                continue;
            }

            $statuses = \json_decode($status_permissions);

            foreach ($statuses as $statusId => $value) {
                if ($value == 2) {
                    array_push($editableIds, $statusId);
                }
            }
        }

        $statuses = OrderStatus::query()->currentShop()->select('id', 'level')->get();
        $deliveryStatus = OrderStatus::query()->whereKey(DELIVERY_ORDER_STATUS_ID)->first();
        $ids = [];
        foreach ($statuses as $status) {
            if ($status->id != DELIVERY_ORDER_STATUS_ID && $status->level <= $deliveryStatus->level) {
                if (in_array($status->id, $editableIds)) {
                    array_push($ids, $status->id);
                }
            }
        }

        return $ids;
    }

    public function isGroupLeader()
    {
        return !empty(UserGroup::query()->where('admin_user_id', $this->id)->first());
    }

    public function canDeleteOrders($ids)
    {

    }

    public function order()
    {
        return $this->hasMany(Order::class, 'assigned_user_id', 'id');
    }
    public function confirmationSingleLatch()
    {
        return $this->hasMany(Order::class, 'close_user_id', 'id');
    }
    public function deliveryOrder()
    {
        return $this->hasMany(Order::class, 'delivery_user_id', 'id');
    }
    public function cancelOrder()
    {
        return $this->hasMany(Order::class, 'cancel_user_id', 'id');
    }

    // Check xem đơn hàng có bị che số điện thoại ko? Chỉ app dụng khi có quyền bungdon3 - Bung đơn màn hình chính
    public function canViewFullPhoneOfOrder(Order $order)
    {
        $user = $this;

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->hasPermission('bungdon3')) {
            return true;
        }

        // Nếu là người tạo, người chuyển, người chốt của đơn hàng thì được xem full số
        if ($order->user_created == $user->id || $order->delivery_user_id == $user->id || $order->close_user_type == $user->id) {
            return true;
        }

        if ($order->assigned_user_id == $user->id || $order->upsale_from_user_id == $user->id || $order->cancel_user_id == $user->id) {
            return true;
        }

        return false;
    }

    public function canEditOrder($order)
    {
        $user = $this;
        if ($user->isAdmin()) {
            return true;
        }

        // Nếu là người tạo, người chuyển, người chốt của đơn hàng thì được xem full số
        if ($order->user_created == $user->id || $order->delivery_user_id == $user->id || $order->close_user_type == $user->id || $order->marketing_id == $user->id) {
            return true;
        }

        if ($user->isOnlySale() && !\in_array($order->status_id, $user->getEditableStatusIds())) {
            return false;
        }

        /*
        if ($user->hasPermission('edit_order')) {
            return true;
        }
        */

        if ($order->assigned_user_id == $user->id || $order->upsale_from_user_id == $user->id || $order->cancel_user_id == $user->id) {
            return true;
        }

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function isUsingCloudfone() {
        return !(empty($this->cloudfone_code)) && (!empty($this->active_cloudfone)) && !$this->isAdmin();
    }

}
