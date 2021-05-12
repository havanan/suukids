git clone https://gitlab.com/khoinv.kpwzto/shop-manager.git tungnt -b tungnt
composer install && npm i
cp .env.example .env
* config env *
VTPOST_BASE_URL="https://partner.viettelpost.vn/v2"
php artisan key:generate
php artisan migrate
gulp
php artisan vtpost:sync_location
php artisan serve
* browse 127.0.0.1:8000 *
{
  "DATA":{
    "ORDER_NUMBER":"DMK83559",
    "ORDER_REFERENCE":"TKS1801492",
    "ORDER_STATUSDATE":"13/12/2018 17:34:05",
    "ORDER_STATUS":501,
    "STATUS_NAME":"Tồn - Thông báo chuyển hoàn bưu cục gốc",
    "LOCATION_CURRENTLY":"TT Quận 1 - Hồ Chí Minh",
    "NOTE":"Giao cho bưu cục",
    "MONEY_COLLECTION":1500000,
    "MONEY_FEECOD":0,
    "MONEY_TOTAL":45650,
    "EXPECTED_DELIVERY":"Khoảng 2 ngày làm việc",
    "PRODUCT_WEIGHT":245,
    "ORDER_SERVICE":"SCOD"
  },
  "TOKEN":""
}