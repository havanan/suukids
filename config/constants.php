<?php
// API meta code
if (!defined('HTTP_STATUS_SUCCESS')) {
    define('HTTP_STATUS_SUCCESS', 200);
}

if (!defined('HTTP_STATUS_SUCCESS_NO_CONTENT')) {
    define('HTTP_STATUS_SUCCESS_NO_CONTENT', 204);
}

if (!defined('HTTP_STATUS_BAD_REQUEST')) {
    define('HTTP_STATUS_BAD_REQUEST', 400);
}

if (!defined('HTTP_STATUS_UNAUTHORIZED')) {
    define('HTTP_STATUS_UNAUTHORIZED', 401);
}

if (!defined('HTTP_STATUS_FORBIDDEN')) {
    define('HTTP_STATUS_FORBIDDEN', 403);
}

if (!defined('HTTP_STATUS_NOT_FOUND')) {
    define('HTTP_STATUS_NOT_FOUND', 404);
}

if (!defined('HTTP_STATUS_METHOD_NOT_ALLOW')) {
    define('HTTP_STATUS_METHOD_NOT_ALLOW', 405);
}

if (!defined('HTTP_STATUS_NOT_ACCEPT')) {
    define('HTTP_STATUS_NOT_ACCEPT', 406);
}

if (!defined('HTTP_STATUS_WRONG_PARAM')) {
    define('HTTP_STATUS_WRONG_PARAM', 412);
}

// Roles manager modules
if (!defined('ROLES')) {
    define('ROLES', [
        'superadmin' => 0,
        'manager' => 1,
    ]);
}

// Sender Type
if (!defined('SENDER_TYPE_ADMIN')) {
    define('SENDER_TYPE_ADMIN', 'admin');
}

if (!defined('SENDER_TYPE_USER')) {
    define('SENDER_TYPE_USER', 'user');
}

// Status
if (!defined('INACTIVE')) {
    define('INACTIVE', 0);
}

if (!defined('ACTIVE')) {
    define('ACTIVE', 1);
}

// Week
if (!defined('WEEKS')) {
    define('WEEKS', [
        '0' => "Monday",
        '1' => "Tuesday",
        '2' => "Wednesday",
        '3' => "Thursday",
        '4' => "Friday",
        '5' => "Saturday",
        '6' => "Sunday",
    ]);
}

if (!defined('MONTH')) {
    define('MONTH', [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July ',
        'August',
        'September',
        'October',
        'November',
        'December',
    ]);
}
if (!defined('MONTH_NUMBER')) {
    define('MONTH_NUMBER', [
        '01',
        '02',
        '03',
        '04',
        '05',
        '06',
        '07',
        '08',
        '09',
        '10',
        '11',
        '12',
    ]);
}
// Paginate
if (!defined('LIMIT_PAGINATE')) {
    define('LIMIT_PAGINATE', [
        10, 25, 50, 100,
    ]);
}

if (!defined('USER_TYPE')) {
    define('USER_TYPE', [
        'manager' => 0,
        'superadmin' => 1,
    ]);
}

if (!defined('ACCESS_IMAGE_EXTENSION')) {
    define('ACCESS_IMAGE_EXTENSION', [
        'png', 'jpg', 'jpeg', 'gif',
    ]);
}

if (!defined('IMAGE_TYPE_FOLDER')) {
    define('IMAGE_TYPE_FOLDER', [
        1 => 'default',
        2 => 'small',
    ]);
}
if (!defined('IMAGE_DEFAULT_TYPE')) {
    define('IMAGE_DEFAULT_TYPE', 1);
}

if (!defined('IMAGE_THUMBNAIL_TYPE')) {
    define('IMAGE_THUMBNAIL_TYPE', 2);
}

if (!defined('IMAGE_ORIGIN_TYPE')) {
    define('IMAGE_ORIGIN_TYPE', 3);
}

if (!defined('IMAGE_DEFAULT_AVATAR_TYPE')) {
    define('IMAGE_DEFAULT_AVATAR_TYPE', 4);
}

//PERMISSION TITLE
if (!defined('PERMISSIONS_TITLE')) {
    define('PERMISSIONS_TITLE', [
        'sale_flag' => 'Sale',
        'customer_manager_flag' => 'Quản lý khách hàng',
        'view_revenue_sale_flag' => 'Xem doanh thu SALE',
        'share_orders_flag' => 'Chia đơn',
        'bung_don_flag' => 'Bung đơn',
        'bung_don2_flag' => 'Bung đơn (Không bung thành công và chuyển hàng)',
        'bung_don_3_flag' => 'Bung đơn màn hình chính',
        'customer_care_flag' => 'Chăm sóc khách hàng',
        'marketing_flag' => 'Marketing',
        'marketing_manager_flag' => 'Quản lý marketing',
        'view_revenue_marketing_flag' => 'Xem doanh thu mkt',
        'export_excel_flag' => 'Xuất excel',
        'accountant_flag' => 'Kế toán',
        'stock_out_flag' => 'Xuất kho',
        'stock_manager_flag' => 'Quản lý kho',
        'disable_edit_upsale_flag' => 'Chặn thay đổi người Up sale',
    ]);
}

if (!defined('ORDER_STATUS_PERMISSIONS_TITLE')) {
    define('ORDER_STATUS_PERMISSIONS_TITLE', [
        '1' => 'Chỉ xem',
        '2' => 'Xem + sửa',
    ]);
}

if (!defined('ORDER_STATUS_PERMISSIONS_COLOR')) {
    define('ORDER_STATUS_PERMISSIONS_COLOR', [
        '2' => '#7FFFD4',
        '1' => '#FAEBD7',
    ]);
}

//VIEW DEFINE
if (!defined('VIEW_ADMIN_PROFILE_PERMISSION')) {
    define('VIEW_ADMIN_PROFILE_PERMISSION', 'admin.profile.permission.');
}

if (!defined('VIEW_ADMIN_SELL_ORDER')) {
    define('VIEW_ADMIN_SELL_ORDER', 'admin.sell.order.');
}
//User color
if (!defined('USER_COLOR')) {
    define('USER_COLOR', [
        '#fff' => 'White',
        '#7bd148' => 'Green',
        '#5484ed' => 'Bold blue',
        '#a4bdfc' => 'Bold',
        '#46d6db' => 'Turquoise',
        '#7ae7bf' => 'Light green',
        '#51b749' => 'Bold green',
        '#fbd75b' => 'Yellow',
        '#ffb878' => 'Orange',
        '#ff887c' => 'Red',
        '#dc2127' => 'Bold red',
        '#dbadff' => 'Purple',
        '#e1e1e1' => 'Gray',
    ]);
}
if (!defined('USER_SEX')) {
    define('USER_SEX', [
        '0' => 'Chưa xác định',
        '1' => 'Nam',
        '2' => 'Nữ',
    ]);
}

// Quyền trong database có thể làm những gì
if (!defined('PERMISSION_CONFIG')) {
    //Danh sách các quyền nhỏ
    define('PERMISSION_CONFIG', [
        'sale_flag' => ['view_orders', /*'assign_order_for_sale',*/'sub_quick_edit', 'edit_order'],
        'customer_manager_flag' => ['manager_customer'],
        'view_revenue_sale_flag' => ['view_report_sale'],
        'share_orders_flag' => ['share_orders', 'assign_order_for_sale', 'edit_order'],
        'bung_don_flag' => ['bungdon'],
        'bung_don2_flag' => ['bungdon2'],
        'bung_don_3_flag' => ['bungdon3'],
        'customer_care_flag' => [],
        'marketing_flag' => ['quick_edit', 'import_excel'],
        'marketing_manager_flag' => ['view_report_marketing', 'import_excel', 'quick_edit'],
        'view_revenue_marketing_flag' => ['view_report_marketing'],
        'export_excel_flag' => ['export_excel', 'import_excel'],
        'accountant_flag' => [],
        'stock_out_flag' => [],
        'stock_manager_flag' => [
            'stock_in', 'stock_out', 'define_warehouse', 'define_supplier',
        ],
        'disable_edit_upsale_flag' => ['disable_edit_upsale_flag'],
        'accountant_flag' => ['import_billWay'],
    ]);
}
if (!defined('PRODUCT_STATUS')) {
    define('PRODUCT_STATUS', [
        '1' => 'Kinh doanh',
        '0' => 'Ngừng kinh doanh',

    ]);
}
if (!defined('STOCK_IN')) {
    define('STOCK_IN', 0);
}

if (!defined('STOCK_OUT')) {
    define('STOCK_OUT', 1);
}
//Excel format file
if (!defined('Excel_ORDER_IMPORT_FORMAT')) {
    define('Excel_ORDER_IMPORT_FORMAT', [
        'stt',
        'ho_ten',
        'so_dien_thoai',
        'dia_chi',
        'san_pham',
        'note_chung',
        'note_2',
        'gia_tien',
        // 'ngay_tao',
    ]);
}

if (!defined('Excel_ORDER_IMPORT_BILLWAY_FORMAT')) {
    define('Excel_ORDER_IMPORT_BILLWAY_FORMAT', [
        'stt',
        'ma',
        'ma_vd',
        'khach_hang',
        'so_dien_thoai',
        'dia_chi',
        'san_pham',
        'trang_thai',
        'tong_tien',
    ]);
}

if (!defined('Excel_ORDER_IMPORT_COLLECT_MONEY_FORMAT')) {
    define('Excel_ORDER_IMPORT_COLLECT_MONEY_FORMAT', [
        'ma_vd',
        'ngay_thu_tien',
    ]);
}

if (!defined('Excel_IMPORT_FORMAT')) {
    define('Excel_IMPORT_FORMAT', [
        'ma_san_pham',
        'ten_san_pham',
        'gia',
        'mau',
        'size',
        'loai',
        'don_vi',
        'so_luong',
    ]);
}

if (!defined('LIMIT_COUNT_ROWS_IMPORT')) {
    define('LIMIT_COUNT_ROWS_IMPORT', 400);
}
if (!defined('ORDER_LIMIT_COUNT_ROWS_IMPORT')) {
    define('ORDER_LIMIT_COUNT_ROWS_IMPORT', 4000);
}
//goi máy bậnCall busy
if (!defined('CALL_BUSY_STATUS_ID')) {
    define('CALL_BUSY_STATUS_ID', 1);
}
//chưa xác nhận
if (!defined('NO_PROCESS_ORDER_STATUS_ID')) {
    define('NO_PROCESS_ORDER_STATUS_ID', 2);
}
//hủy
if (!defined('CANCEL_ORDER_STATUS_ID')) {
    define('CANCEL_ORDER_STATUS_ID', 3);
}
//chuyển hàng
if (!defined('DELIVERY_ORDER_STATUS_ID')) {
    define('DELIVERY_ORDER_STATUS_ID', 4);
}
//xác nhận - chốt đơn
if (!defined('CLOSE_ORDER_STATUS_ID')) {
    define('CLOSE_ORDER_STATUS_ID', 5);
}
//chuyển hoàn
if (!defined('REFUND_ORDER_STATUS_ID')) {
    define('REFUND_ORDER_STATUS_ID', 6);
}
//thành công
if (!defined('COMPLETE_ORDER_STATUS_ID')) {
    define('COMPLETE_ORDER_STATUS_ID', 7);
}
//không nghe máy
if (!defined('UNTOUCH_ORDER_STATUS_ID')) {
    define('UNTOUCH_ORDER_STATUS_ID', 8);
}
//kế toán mặc định
if (!defined('ACCOUNTANT_DEFAULT_ORDER_STATUS_ID')) {
    define('ACCOUNTANT_DEFAULT_ORDER_STATUS_ID', 9);
}
//đã thu tiền
if (!defined('COLLECT_MONEY_ORDER_STATUS_ID')) {
    define('COLLECT_MONEY_ORDER_STATUS_ID', 10);
}
//đã trả kho
if (!defined('RETURNED_STOCK_STATUS_ID')) {
    define('RETURNED_STOCK_STATUS_ID', 11);
}
//tham khảo suy nghĩ thêm
if (!defined('CONSIDER_ORDER_STATUS_ID')) {
    define('CONSIDER_ORDER_STATUS_ID', 23);
}
//Đơn cũ
if (!defined('OLD_ORDER_STATUS_ID')) {
    define('OLD_ORDER_STATUS_ID', 32);
}

if (!defined('MOVE_PRODUCT')) {
    define('MOVE_PRODUCT', 1);
}
if (!defined('STOCK_OUT_PRODUCT')) {
    define('STOCK_OUT_PRODUCT', 0);
}
if (!defined('BUSINESS')) {
    define('BUSINESS', 1);
}
if (!defined('DEFAULT_STOCK_GROUP')) {
    define('DEFAULT_STOCK_GROUP', 1);
}
if (!defined('STOP_BUSINESS')) {
    define('STOP_BUSINESS', 2);
}

if (!defined('KHO_TONG_ID')) {
    define('KHO_TONG_ID', 1);
}

if (!defined('SEND_NOTIFICATION_ORDER_STATUS_IDS')) {
    define('SEND_NOTIFICATION_ORDER_STATUS_IDS', [CLOSE_ORDER_STATUS_ID, DELIVERY_ORDER_STATUS_ID, COMPLETE_ORDER_STATUS_ID]);
}

if (!defined('CUSTOMER_TYPE')) {
    define('CUSTOMER_TYPE', [
        '1' => 'Mới',
        '2' => 'Tiếp tục mua lại',
        '3' => 'Gọi lại',
    ]);
}
if (!defined('REVENUE_TYPE')) {
    define('REVENUE_TYPE', [
        '0' => 'Theo doanh thu',
        '1' => 'Không tính doanh thu'
    ]);
}
if (!defined('CUSTOMER_EMOTIONS')) {
    define('CUSTOMER_EMOTIONS', [
        '1' => 'Bình thường',
        '2' => 'Vui vẻ',
        '3' => 'Bực tức',
    ]);
}
if (!defined('INTERNAL_EXPORT')) {
    define('INTERNAL_EXPORT', 1);
}
if (!defined('NORMAL_EXPORT')) {
    define('NORMAL_EXPORT', 2);
}
if (!defined('CUSTOMER_CARE')) {
    define('CUSTOMER_CARE', [
        '0' => 'Gọi thành công',
        '1' => 'Không nghe máy',
        '2' => 'Thuê bao',
        '3' => 'Sai số',
        '4' => 'Không tin hiệu',
        '5' => 'KH từ chối',
        '6' => 'Gọi lại',
        '7' => 'Máy bận',
        '8' => 'Tắt máy ngang',
        '9' => 'KH gọi nhỡ',
        '10' => 'KH chốt hẹn',
        '11' => 'Lý do đặc biệt',
    ]);
}

//Loại lịch sử: 1: Mở đơn hàng, 2: Gán cho tài khoản, 3: Chuyển trạng thái, 4: Sửa sản phẩm, 5: Sửa các thông số khác
if (!defined('ORDER_HISTORY_TYPE')) {
    define('ORDER_HISTORY_TYPE', [
        '1' => 'Mở đơn hàng',
        '2' => 'Gán cho tài khoản',
        '3' => 'Chuyển trạng thái',
        '4' => 'Sửa sản phẩm',
        '5' => 'Sửa các thông số khác',
        '6' => 'Địa chỉ',
        '7' => 'Sản phẩm',
        '8' => 'Số điện thoại',
        '9' => 'Số lượng sản phẩm',
        '10' => 'Tên khách hàng',
        '11' => 'Email khách hàng',
        '12' => 'Ghi chú chung',
        '13' => 'Người tạo đơn',
        '14' => 'Nguồn Up sale',
        '15' => 'Chia đơn cho',
        '16' => 'Lý do hủy',
        '17' => 'Phụ thu',
        '18' => 'Phí vận chuyển',
        '19' => 'Ghi chú 2',
    ]);
}

if (!defined('ORDER_TEXT')) {
    define('ORDER_TEXT', [
        'stt' => 'STT',
        'assigned_user' => 'NV được chia',
        'code' => 'Mã',
        'shipping_code' => 'Mã VĐ',
        'source' => 'Nguồn',
        'bundle' => 'Loại đơn',
        'customer' => 'Khách hàng',
        'customer.phone' => 'Số điện thoại',
        'customer.returned' => 'Lần mua',
        'customer.call.history' => 'Lịch sử gọi',
        'customer.address' => 'Địa chỉ',
        'location_vtp.location_currently' => 'Vị trí hiện tại',
        'location_ems.locate' => 'Vị trí hiện tại',
        'order_products' => 'Sản phẩm',
        'note1' => 'Ghi chú chung',
        'note2' => 'Ghi chú khác',
        'status' => 'Trạng thái',
        'total_price' => 'Tổng tiền',
        'close_user.account_id' => 'NV Xác nhận',
        'close_date' => 'Ngày Xác nhận',
        'complete_date' => 'Ngày thành công',
        'refund_date' => 'Ngày hoàn',
        'delivery_user.account_id' => 'Người chuyển',
        'delivery_date' => 'Ngày chuyển',
        'user_created_obj.name' => 'Người tạo',
        'create_date' => 'Ngày tạo',
        'shipping_note' => 'Note giao hàng',
        'shipping_service.name' => 'Hình thức giao',
        'shop_name' => 'Công ty',
        'upsale_from_user.account_id' => 'Nguồn Up Sale',
    ]);
}
if (!defined('ACC_STATUS')) {
    define('ACC_STATUS', [
        '' => 'Tất cả',
        '1' => 'Tài khoản kích hoạt',
        '0' => 'Tài khoản chưa kích hoạt',
    ]);
}
if (!defined('MKT_SOURCE_VIEW_BY')) {
    define('MKT_SOURCE_VIEW_BY', [
        '' => 'Xem theo',
        'close_date' => 'Ngày xác nhận',
        'assign_accountant_date' => 'Ngày chuyển kế toán',
    ]);
}
if (!defined('ORDER_SORT_DEFAULT')) {
    define('ORDER_SORT_DEFAULT', '[{ "name": "stt", "show": 1 }, { "name": "assigned_user", "show": 1 }, { "name": "code", "show": 1 }, { "name": "shipping_code", "show": 1 }, { "name": "source", "show": 1 }, { "name": "customer", "show": 1 }, { "name": "customer.phone", "show": 1 }, { "name": "customer.returned", "show": 1 }, { "name": "bundle", "show": 1 }, { "name": "customer.call.history", "show": 0}, { "name": "customer.address", "show": 1 }, { "name": "order_products", "show": 1 }, { "name": "note1", "show": 1 }, { "name": "note2", "show": 1 }, { "name": "status", "show": 1 }, { "name": "total_price", "show": 1 }, { "name": "close_user.account_id", "show": 1 }, { "name": "close_date", "show": 1 }, { "name": "delivery_user.account_id", "show": 1 }, { "name": "complete_date", "show": 1 }, { "name": "refund_date", "show": 1 }, { "name": "delivery_date", "show": 1 }, { "name": "user_created_obj.name", "show": 1 }, { "name": "create_date", "show": 1 }, { "name": "shipping_note", "show": 1 }, { "name": "shipping_service.name", "show": 1 }, { "name": "shop_name", "show": 1 }, { "name": "upsale_from_user.account_id", "show": 1 }]');
}

if (!defined('DEFAULT_ORDER_SOURCE_ID')) {
    define('DEFAULT_ORDER_SOURCE_ID', 3);
}

if (!defined('CUSTOMER_GROUP_FAMILIAR_ID')) {
    define('CUSTOMER_GROUP_FAMILIAR_ID', 3);
}

if (!defined('NO_REVENUE_FLAG')) {
    define('NO_REVENUE_FLAG', 0);
}

if (!defined('NO_REACH_FLAG')) {
    define('NO_REACH_FLAG', 0);
}

if (!defined('UNCONFIMRED')) {
    define('UNCONFIMRED', 2);
}

if (!defined('STATUS_DON_HANG_CHOT')) {
    define('STATUS_DON_HANG_CHOT', [
        4, 5, 7, 9, 10,
    ]);
}

if (!defined('STATUS_DON_HANG_THANH_CONG')) {
    define('STATUS_DON_HANG_THANH_CONG', [
        7, 10,
    ]);
}

//order type
if (!defined('ORDER_TYPE_NEW')) {
    define('ORDER_TYPE_NEW', 1);
}
if (!defined('ORDER_TYPE_CARE')) {
    define('ORDER_TYPE_CARE', 2);
}
if (!defined('ORDER_TYPE_OPTIMAL')) {
    define('ORDER_TYPE_OPTIMAL', 3);
}
if (!defined('ORDER_TYPE_AGAIN')) {
    define('ORDER_TYPE_AGAIN', 4);
}

if (!defined('STATUS_AN_DON_HANG_SALE_MKT')) {
    define('STATUS_AN_DON_HANG_SALE_MKT', [
        4, 7, 10, 11,
    ]);
}
if (!defined('LOGIN_TIME_FROM')) {
    define('LOGIN_TIME_FROM', "08:00");
}
if (!defined('LOGIN_TIME_TO')) {
    define('LOGIN_TIME_TO', "17:30");
}
if (!defined('SIZES')) {
    define('SIZES', [
        'S','M','L','XL','XXL',59,66,73,80,90
    ]);
}
if (!defined('COLORS')) {
    define('COLORS', [
        0 => 'Hồng',
        1 => 'Xanh',
        2 => 'Vàng',
        3 => 'Trắng'
    ]);
}
