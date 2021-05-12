<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderSource;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\OrderStatus;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Reminder;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\ProductBundle;
use App\Models\OrderType;
use App\Models\Scopes\ShopScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CurrentShopScope;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use SoftDeletes;
    use CurrentShopScope;

    protected $table = 'orders';
    protected $fillable = [  'code', 'shipping_code', 'customer_id', 'note1', 'note2', 'shipping_note',
                             'is_top_priority', 'is_send_sms', 'is_inner_city',
                             'status_id', 'shipping_service_id', 'bundle_id', 'source_id', 'type',
                             'user_created', 'upsale_from_user_id', 'assigned_user_id',
                             'cancel_note', 'price', 'discount_price',
                             'shipping_price', 'other_price', 'total_price',
                             'create_date', 'share_date', 'close_date', 'assign_accountant_date',
                             'delivery_date', 'complete_date', 'collect_money_date', 'province_id', 'district_id', 'ward_id',
                             'close_user_id', 'close_user_type', 'delivery_user_id', 'delivery_user_type',
                             'refund_date', 'user_created', 'create_user_type', 'duplicated', 'close_duplicated_order_id',
                             'cancel_user_id', 'cancel_user_type', 'cancel_date',
                             'shop_id', 'phone', 'is_old_customer', 'created_at','returned'];

    function assigned_user() {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    function upsale_from_user() {
        return $this->belongsTo(User::class, 'upsale_from_user_id');
    }

    function source() {
        return $this->belongsTo(OrderSource::class, 'source_id');
    }

    function customer() {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    function shipping_service() {
        return $this->belongsTo(DeliveryMethod::class, 'shipping_service_id');
    }

    function status() {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    function order_products() {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    function products() {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id');
    }

    function user_created_obj() {
        return $this->belongsTo(User::class, 'user_created');
    }

    function close_user() {
        return $this->morphTo('close_user');
    }

    function delivery_user() {
        return $this->morphTo('delivery_user');
    }

    public function type_obj() {
        return $this->belongsTo(OrderType::class, 'type');
    }

    function reminders() {
        return $this->hasMany(Reminder::class, 'order_id');
    }

    public function orderStatus()
    {
        return $this->hasOne(OrderStatus::class,'status_id','id');
    }

    public function province() {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district() {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function ward() {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function bundle() {
        return $this->belongsTo(ProductBundle::class, 'bundle_id');
    }

    public function orderStatusName()
    {
        return $this->hasOne(OrderStatus::class,'id','status_id');
    }

    public function getProductsNameAttribute() {
        $name = '';
        if (!empty($this->order_products)) {
            foreach ($this->order_products as $orderProduct) {
                $product = $orderProduct->product;
                if (!empty($product)) {
                    $productName = $orderProduct->quantity . ' ' . $orderProduct->product->name . ($orderProduct->product->size ? ' size ' .  $orderProduct->product->size : '');
                    $name .= !empty($name) ? (', ' . $productName) : $productName;
                }
            }
        }

        return $name;
    }
    public static function getMktSourceIndex(){
        return [
            ['code'=> 'lead', 'text'=>'Lượng số'],
            ['code'=> 'cost_per_lead', 'text'=>'Chi phí 1 số'],
            ['code'=> 'orders', 'text'=>'Số đơn'],
            ['code'=> 'close_rate', 'text'=>'Tỷ lệ chốt'],
            ['code'=> 'rate_of_return', 'text'=>'Chi phí/Doanh thu'],
        ];
    }
    public static function getMktOverviewIndex(){
        return [
            ['code'=> 'cost', 'text'=>'Chi phí ADS'],
            ['code'=> 'lead', 'text'=>'Lượng số'],
            ['code'=> 'cost_per_lead', 'text'=>'Chi phí 1 số'],
            ['code'=> 'orders', 'text'=>'Số đơn'],
            ['code'=> 'close_rate', 'text'=>'Tỷ lệ chốt'],
            ['code'=> 'rpu', 'text'=>'Giá trị 1 đơn TB'],
            ['code'=> 'revenue', 'text'=>'Doanh số bán mới'],
            ['code'=> 'rate_of_return', 'text'=>'Chi phí/Doanh thu'],
            ['type'=>'input','code'=> 'bonus_percent', 'text'=>'Thưởng % DS'],
            ['type'=>'input','code'=> 'labor_day', 'text'=>'Ngày công'],
            ['type'=>'input','code'=> 'wage', 'text'=>'Lương cứng'],
            ['code'=> 'total_wage', 'text'=>'Lương theo ngày công'],
            ['type'=> 'input','code'=> 'bonus', 'text'=>'Thưởng (thanh toán theo quý)'],
            ['code'=> 'total', 'text'=>'Tổng'],
        ];
    }

    public function locations_vtp()
    {
        return $this->hasMany(VTPOSTWebHookLog::class, 'order_number', 'shipping_code');
    }

    public function location_vtp()
    {
        return $this->hasOne(VTPOSTWebHookLog::class, 'order_number', 'shipping_code')->latest();
    }

    public function locations_ems() {
        return $this->hasMany(EMSWebhookLog::class, 'tracking_code', 'shipping_code');
    }

    public function location_ems() {
        return $this->hasOne(EMSWebhookLog::class, 'tracking_code', 'shipping_code')->latest();
    }
}


