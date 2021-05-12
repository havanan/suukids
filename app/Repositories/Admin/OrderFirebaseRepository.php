<?php

namespace App\Repositories\Admin;

use App\Helpers\FirebaseHelper;
use App\Models\FcmToken;
use App\Models\Order;
use App\Repositories\BaseRepository;
use Log;

class OrderFirebaseRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function sendNotificationIfNeed($orders)
    {
        try {
            if (empty($orders)) {
                return null;
            }

            $tokens = FcmToken::whereHas('user', function ($q) {
                $q->where(function ($q1) {
                    $q1->where('type', 0)->orWhere('shop_manager_flag', 1);
                })->onlyCurrentShop();
            })->get();

            if (empty($tokens)) {
                return;
            }

            $tokens = $tokens->pluck('token')->toArray();

            foreach ($orders as $order) {
                $sale = $order->assigned_user;

                if (empty($sale)) {
                    if (!empty($order->user_created_obj) && $order->user_created_obj->isSale()) {
                        $sale = $order->user_created_obj;
                    } else {
                        continue;
                    }
                }

                $message = '';
                if ($order->status_id == CLOSE_ORDER_STATUS_ID) {
                    $message = "chốt thành công đơn hàng";
                }
                if ($order->status_id == DELIVERY_ORDER_STATUS_ID) {
                    $message = 'bán thành công đơn hàng';
                }
                if ($order->status_id == 7) {
                    $message = 'bán thành công đơn hàng';
                }
                FirebaseHelper::sendMessageTo($tokens, $sale->account_id . ': ' . $sale->name . ' ' . $message . ' trị giá ' . number_format($order->total_price) . ' đ');
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}