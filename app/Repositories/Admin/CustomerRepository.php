<?php

namespace App\Repositories\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Repositories\BaseRepository;
use Log;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function findOrCreate($data, $orderId = null)
    {
        $user = getCurrentUser();
        $query = $this->model::query()->onlyCurrentShop()->where(function ($q) use ($data) {
            $q->where('phone', $data['phone']);
            if (!empty($data['phone2'])) {
                $q->orWhere('phone2', $data['phone2']);
            }
        });

        $customer = $query->first();
        $oldData = $query->first();
        $updateData = [];
        if (empty($customer)) {
            $data['created_by'] = $user->id;
            $data['shop_id'] = $user->shop_id;

            $save = $this->model::query()->create($data);

            $ret = [
                'customerId' => $save->id,
                'updateData' => []
            ];

            return $ret;
        } else {
            // if (empty($customer->customer_group_id)) {
                $closeStatus = OrderStatus::query()->whereKey(CLOSE_ORDER_STATUS_ID)->first();
                $newOrderQuery = Order::query()->onlyCurrentShop()->where('customer_id', $customer->id)->whereHas('status', function ($q) use ($closeStatus) {
                    $q->where('level', '>=', $closeStatus->level);
                });
                if (!empty($orderId)) {
                    $newOrderQuery = $newOrderQuery->where('id', '<>', $orderId);
                }
                $newOrder = $newOrderQuery->first();
                if (!empty($newOrder)) {
                    $data['customer_group_id'] = CUSTOMER_GROUP_FAMILIAR_ID;
                }
            // }
            $customer->fill($data);
            $changes = $customer->getDirty();
            $customer->save();

            foreach ($changes as $field => $value) {
                $updateData[$field] = [
                    'old_value' => !empty($oldData[$field]) ? $oldData[$field] : '',
                    'new_value' => $value,
                ];
            }
        }

        $ret = [
            'customerId' => $customer->id,
            'updateData' => $updateData,
        ];

        return $ret;
    }

}
