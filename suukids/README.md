# Dự án website shop manager

## Mục lục

* [Môi trường phát triển](#enviroment)

* [Cài đặt & chạy dự án](#install)


<a id="enviroment"></a>
## Môi trường

### Development

Laravel v5.7

Nginx

PHP

MariaDB

Biên dịch css bằng gulp

### Production

Nginx

PHP-FPM 7

Mysql


<a id="install"></a>
## Cài đặt và chạy dự án


Clone dự án từ gitlab

```
git clone https://gitlab.com/khoinv.kpwzto/shop-manager.git
```

Vào thư mục

```
cd shop-manager
```

Cài đặt các components qua composer

```
composer install
```

Cài đặt database và migrate

```
php artisan migrate
```

Cài node modules sử dụng gulp file

```
Cài nvm để sử dụng node 10.16.3 / Trên window thì cài luôn node 10.16.3
curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
source ~/.profile  
nvm install 10.16.3
nvm use 10.16.3
npm install gulp-cli -g
Chạy lệnh gulp:
gulp
Tự động gulp:
gulp watch
```

Khởi chạy trên môi trường local

```
php artisan serve
```


