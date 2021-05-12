<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Login
Route::get('login', 'Auth\LoginController@loginForm')->name('login');
Route::post('login', 'Auth\LoginController@postLogin')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('ems/update-order', 'EMSWebhookController@updateStatus');
//Route::post('vtpost/update-order', 'VTPostWebhookController@updateStatus');
Route::post('vtpost/sync-transport', 'VTPostWebhookController@updateStatus');
//Web Route
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'], function () {
    Route::group(['middleware' => ['auth:users', 'check-login-time']], function () {
        include "web/admin/dashboard.php";
        include "web/admin/profile.php";
        include "web/admin/order_source.php";
        include "web/admin/order_status.php";
        include "web/admin/user.php";
        include "web/admin/cost.php";
        include "web/admin/user-group.php";
        include "web/admin/product.php";
        include "web/admin/sell.php";
        include "web/admin/stock.php";
        Route::get('address/district/api-search', 'AddressController@apiSearchDistrict')->name('address.district.api-search');
        Route::get('address/ward/api-search', 'AddressController@apiSearchWard')->name('address.ward.api-search');
        include "web/admin/customer.php";
        include "web/admin/manager.php";
        include "web/admin/report.php";
        include "web/admin/hook_log.php";
        include "web/admin/export_log.php";
        include "web/admin/config.php";
        include "web/admin/vtp.php";
        include "web/admin/action_log.php";
        include "web/admin/login_log.php";
        include "web/admin/reminder.php";
        /* Report */
        Route::get('report', 'ReportController@index')->name('report.index');
        /* End Report */
        /* Firebase */
        Route::post('set-device-token', 'FirebaseController@setDeviceToken')->name('set-device-token');
    });
});

Route::group(['prefix' => 'quan-ly-shop', 'as' => 'superadmin.', 'namespace' => 'SuperAdmin'], function () {
    Route::group(['middleware' => ['auth:superadmin', 'check-superadmin']], function () {
        include "web/superadmin/shop.php";
        include "web/superadmin/report.php";
        include "web/superadmin/profile.php";
        include "web/superadmin/action_log.php";
        include "web/superadmin/login_log.php";

        Route::get('/', function () {
            return redirect(route('superadmin.shop.index'));
        });
    });

    Route::group(['namespace' => 'Auth'], function () {
        Route::get('login', 'LoginController@loginForm')->name('login');
        Route::post('login', 'LoginController@postLogin')->name('login.post');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
});
