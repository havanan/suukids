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
        'customer_manager_flag' => 'Qu???n l?? kh??ch h??ng',
        'view_revenue_sale_flag' => 'Xem doanh thu SALE',
        'share_orders_flag' => 'Chia ????n',
        'bung_don_flag' => 'Bung ????n',
        'bung_don2_flag' => 'Bung ????n (Kh??ng bung th??nh c??ng v?? chuy???n h??ng)',
        'bung_don_3_flag' => 'Bung ????n m??n h??nh ch??nh',
        'customer_care_flag' => 'Ch??m s??c kh??ch h??ng',
        'marketing_flag' => 'Marketing',
        'marketing_manager_flag' => 'Qu???n l?? marketing',
        'view_revenue_marketing_flag' => 'Xem doanh thu mkt',
        'export_excel_flag' => 'Xu???t excel',
        'accountant_flag' => 'K??? to??n',
        'stock_out_flag' => 'Xu???t kho',
        'stock_manager_flag' => 'Qu???n l?? kho',
        'disable_edit_upsale_flag' => 'Ch???n thay ?????i ng?????i Up sale',
    ]);
}

if (!defined('ORDER_STATUS_PERMISSIONS_TITLE')) {
    define('ORDER_STATUS_PERMISSIONS_TITLE', [
        '1' => 'Ch??? xem',
        '2' => 'Xem + s???a',
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
        '0' => 'Ch??a x??c ?????nh',
        '1' => 'Nam',
        '2' => 'N???',
    ]);
}

// Quy???n trong database c?? th??? l??m nh???ng g??
if (!defined('PERMISSION_CONFIG')) {
    //Danh s??ch c??c quy???n nh???
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
        '0' => 'Ng???ng kinh doanh',

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
//goi m??y b???nCall busy
if (!defined('CALL_BUSY_STATUS_ID')) {
    define('CALL_BUSY_STATUS_ID', 1);
}
//ch??a x??c nh???n
if (!defined('NO_PROCESS_ORDER_STATUS_ID')) {
    define('NO_PROCESS_ORDER_STATUS_ID', 2);
}
//h???y
if (!defined('CANCEL_ORDER_STATUS_ID')) {
    define('CANCEL_ORDER_STATUS_ID', 3);
}
//chuy???n h??ng
if (!defined('DELIVERY_ORDER_STATUS_ID')) {
    define('DELIVERY_ORDER_STATUS_ID', 4);
}
//x??c nh???n - ch???t ????n
if (!defined('CLOSE_ORDER_STATUS_ID')) {
    define('CLOSE_ORDER_STATUS_ID', 5);
}
//chuy???n ho??n
if (!defined('REFUND_ORDER_STATUS_ID')) {
    define('REFUND_ORDER_STATUS_ID', 6);
}
//th??nh c??ng
if (!defined('COMPLETE_ORDER_STATUS_ID')) {
    define('COMPLETE_ORDER_STATUS_ID', 7);
}
//kh??ng nghe m??y
if (!defined('UNTOUCH_ORDER_STATUS_ID')) {
    define('UNTOUCH_ORDER_STATUS_ID', 8);
}
//k??? to??n m???c ?????nh
if (!defined('ACCOUNTANT_DEFAULT_ORDER_STATUS_ID')) {
    define('ACCOUNTANT_DEFAULT_ORDER_STATUS_ID', 9);
}
//???? thu ti???n
if (!defined('COLLECT_MONEY_ORDER_STATUS_ID')) {
    define('COLLECT_MONEY_ORDER_STATUS_ID', 10);
}
//???? tr??? kho
if (!defined('RETURNED_STOCK_STATUS_ID')) {
    define('RETURNED_STOCK_STATUS_ID', 11);
}
//tham kh???o suy ngh?? th??m
if (!defined('CONSIDER_ORDER_STATUS_ID')) {
    define('CONSIDER_ORDER_STATUS_ID', 23);
}
//????n c??
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
        '1' => 'M???i',
        '2' => 'Ti???p t???c mua l???i',
        '3' => 'G???i l???i',
    ]);
}
if (!defined('REVENUE_TYPE')) {
    define('REVENUE_TYPE', [
        '0' => 'Theo doanh thu',
        '1' => 'Kh??ng t??nh doanh thu'
    ]);
}
if (!defined('CUSTOMER_EMOTIONS')) {
    define('CUSTOMER_EMOTIONS', [
        '1' => 'B??nh th?????ng',
        '2' => 'Vui v???',
        '3' => 'B???c t???c',
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
        '0' => 'G???i th??nh c??ng',
        '1' => 'Kh??ng nghe m??y',
        '2' => 'Thu?? bao',
        '3' => 'Sai s???',
        '4' => 'Kh??ng tin hi???u',
        '5' => 'KH t??? ch???i',
        '6' => 'G???i l???i',
        '7' => 'M??y b???n',
        '8' => 'T???t m??y ngang',
        '9' => 'KH g???i nh???',
        '10' => 'KH ch???t h???n',
        '11' => 'L?? do ?????c bi???t',
    ]);
}

//Lo???i l???ch s???: 1: M??? ????n h??ng, 2: G??n cho t??i kho???n, 3: Chuy???n tr???ng th??i, 4: S???a s???n ph???m, 5: S???a c??c th??ng s??? kh??c
if (!defined('ORDER_HISTORY_TYPE')) {
    define('ORDER_HISTORY_TYPE', [
        '1' => 'M??? ????n h??ng',
        '2' => 'G??n cho t??i kho???n',
        '3' => 'Chuy???n tr???ng th??i',
        '4' => 'S???a s???n ph???m',
        '5' => 'S???a c??c th??ng s??? kh??c',
        '6' => '?????a ch???',
        '7' => 'S???n ph???m',
        '8' => 'S??? ??i???n tho???i',
        '9' => 'S??? l?????ng s???n ph???m',
        '10' => 'T??n kh??ch h??ng',
        '11' => 'Email kh??ch h??ng',
        '12' => 'Ghi ch?? chung',
        '13' => 'Ng?????i t???o ????n',
        '14' => 'Ngu???n Up sale',
        '15' => 'Chia ????n cho',
        '16' => 'L?? do h???y',
        '17' => 'Ph??? thu',
        '18' => 'Ph?? v???n chuy???n',
        '19' => 'Ghi ch?? 2',
    ]);
}

if (!defined('ORDER_TEXT')) {
    define('ORDER_TEXT', [
        'stt' => 'STT',
        'assigned_user' => 'NV ???????c chia',
        'code' => 'M??',
        'shipping_code' => 'M?? V??',
        'source' => 'Ngu???n',
        'bundle' => 'Lo???i ????n',
        'customer' => 'Kh??ch h??ng',
        'customer.phone' => 'S??? ??i???n tho???i',
        'customer.returned' => 'L???n mua',
        'customer.call.history' => 'L???ch s??? g???i',
        'customer.address' => '?????a ch???',
        'location_vtp.location_currently' => 'V??? tr?? hi???n t???i',
        'location_ems.locate' => 'V??? tr?? hi???n t???i',
        'order_products' => 'S???n ph???m',
        'note1' => 'Ghi ch?? chung',
        'note2' => 'Ghi ch?? kh??c',
        'status' => 'Tr???ng th??i',
        'total_price' => 'T???ng ti???n',
        'close_user.account_id' => 'NV X??c nh???n',
        'close_date' => 'Ng??y X??c nh???n',
        'complete_date' => 'Ng??y th??nh c??ng',
        'refund_date' => 'Ng??y ho??n',
        'delivery_user.account_id' => 'Ng?????i chuy???n',
        'delivery_date' => 'Ng??y chuy???n',
        'user_created_obj.name' => 'Ng?????i t???o',
        'create_date' => 'Ng??y t???o',
        'shipping_note' => 'Note giao h??ng',
        'shipping_service.name' => 'H??nh th???c giao',
        'shop_name' => 'C??ng ty',
        'upsale_from_user.account_id' => 'Ngu???n Up Sale',
    ]);
}
if (!defined('ACC_STATUS')) {
    define('ACC_STATUS', [
        '' => 'T???t c???',
        '1' => 'T??i kho???n k??ch ho???t',
        '0' => 'T??i kho???n ch??a k??ch ho???t',
    ]);
}
if (!defined('MKT_SOURCE_VIEW_BY')) {
    define('MKT_SOURCE_VIEW_BY', [
        '' => 'Xem theo',
        'close_date' => 'Ng??y x??c nh???n',
        'assign_accountant_date' => 'Ng??y chuy???n k??? to??n',
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
        0 => 'H???ng',
        1 => 'Xanh',
        2 => 'V??ng',
        3 => 'Tr???ng'
    ]);
}
