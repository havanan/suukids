<?php

Route::group([
    'prefix' => 'vtp', 'namespace' => 'Config', 'middleware' => 'check-admin', 'as' => 'vtp.'
], function () {
    Route::post('/login', 'VtpController@login')->name('vtp.login');
    Route::get('/list-province', 'VtpController@listProvince')->name('vtp.list-province');
    Route::get('/list-district', 'VtpController@listDistrict')->name('vtp.list-district');
    Route::get('/list-ward', 'VtpController@listWard')->name('vtp.list-ward');
    Route::get('/list-buu-cuc', 'VtpController@listBuuCuc')->name('vtp.list-buu-cuc');
});
