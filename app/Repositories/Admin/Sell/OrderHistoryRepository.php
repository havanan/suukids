<?php

namespace App\Repositories\Admin\Sell;

use App\Models\Customer;
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class OrderHistoryRepository extends BaseRepository
{
    public function __construct(OrderHistory $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        return $this->model::query()->create($data);
    }

    public function createFromChanges($orderId, $changes)
    {
        $data = [];

        foreach ($changes as $field => $value) {
            switch ($field) {
                case 'status_id':
                    $statuses = OrderStatus::query()->currentShop()->whereIn('id', array_values($value))->select('id', 'name')->get();

                    $oldStatus = '';
                    $newStatus = '';
                    foreach ($statuses as $status) {
                        if ($status->id == $value['old_value']) {
                            $oldStatus = $status->name;
                        } else {
                            $newStatus = $status->name;
                        }
                    }

                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 3,
                        'message' => 'Thay đổi Trạng thái từ ' . $oldStatus . ' thành ' . $newStatus,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'note1':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 12,
                        'message' => 'Thay đổi Ghi chú chung từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'note2':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 19,
                        'message' => 'Thay đổi Ghi chú 2 từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'upsale_from_user_id':
                    $users = User::query()->whereIn('id', array_values($value))->select('id', 'name')->get();
                    $oldUser = '';
                    $newUser = '';
                    foreach ($users as $user) {
                        if ($user->id == $value['old_value']) {
                            $oldUser = $user->name;
                        } else {
                            $newUser = $user->name;
                        }
                    }
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 14,
                        'message' => 'Thay đổi Nguồn Up Sale từ ' . $oldUser . ' thành ' . $newUser,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'assigned_user_id':
                    $users = User::query()->whereIn('id', array_values($value))->select('id', 'name')->get();
                    $oldUser = '';
                    $newUser = '';
                    foreach ($users as $user) {
                        if ($user->id == $value['old_value']) {
                            $oldUser = $user->name;
                        } else {
                            $newUser = $user->name;
                        }
                    }
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 15,
                        'message' => 'Thay đổi Chia đơn cho từ ' . $oldUser . ' thành ' . $newUser,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'cancel_note':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 16,
                        'message' => 'Thay đổi Lý do hủy / Xem xét lại từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'shipping_price':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 18,
                        'message' => 'Thay đổi Phí vận chuyển từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'other_price':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 17,
                        'message' => 'Thay đổi Phụ thu từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'user_created':
                    $users = User::query()->whereIn('id', array_values($value))->select('id', 'name')->get();
                    $oldUser = '';
                    $newUser = '';
                    foreach ($users as $user) {
                        if ($user->id == $value['old_value']) {
                            $oldUser = $user->name;
                        } else {
                            $newUser = $user->name;
                        }
                    }
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 13,
                        'message' => 'Thay đổi Người tạo đơn từ ' . $oldUser . ' thành ' . $newUser,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'customer_id':
                    $customers = Customer::query()->whereIn('id', array_values($value))->select('id', 'phone')->get();
                    $oldPhone = '';
                    $newPhone = '';
                    foreach ($customers as $customer) {
                        if ($customer->id == $value['old_value']) {
                            $oldPhone = $customer->phone;
                        } else {
                            $newPhone = $customer->phone;
                        }
                    }
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 8,
                        'message' => 'Thay đổi Số điện thoại từ ' . $oldPhone . ' thành ' . $newPhone,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'name':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 10,
                        'message' => 'Thay đổi Tên khách hàng từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'address':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 10,
                        'message' => 'Thay đổi Địa chỉ từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'email':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 10,
                        'message' => 'Thay đổi Email từ ' . (!empty($value['old_value']) ? $value['old_value'] : '') . ' thành ' . $value['new_value'],
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'update_product':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 9,
                        'message' => $value,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'add_product':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 7,
                        'message' => $value,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'remove_product':
                    $data[] = [
                        'order_id' => $orderId,
                        'type' => 7,
                        'message' => $value,
                        'created_by' => getCurrentUser()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                default:
                    break;
            }
        }

        if (!empty($data)) {
            OrderHistory::insert($data);
        }
    }

    public function getByOrderId($orderId)
    {
        return $this->model::query()->with('userCreated')->where('order_id', $orderId)->orderBy('created_at', 'desc')->get();
    }
}
