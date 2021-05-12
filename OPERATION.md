Phân loại sản phẩm (product bundle)
BAO CAO MAKETING THEO NGUON
        // orders join order_sources
        // where orders.deleted_at is NULL and orders.shop_id = 23 and orders.status_id IN STATUS_DON_HANG_CHOT
        // input: marketer_id equal ouput: orders.upsale_from_user_id or orders.marketing_id or orders.user_created
        // input: date_begin date_end between ouput (Ngày xác nhận): orders.close_date
        // input: date_begin date_end between ouput (Ngày chuyển kế toán): orders.assign_accountant_date
        // aggregate count_phone COUNT(orders.id),count_order IN STATUS_DON_HANG_CHOT,sum_price = SUM(orders.total_price) in STATUS_DON_HANG_CHOT,percent = COUNT(orders.status_id IN STATUS_DON_HANG_CHOT) / COUNT(orders.id)
