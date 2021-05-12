<?php

Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
    Route::get('/shop', 'ReportController@shop')->name('shop');
    Route::get('/product', 'ReportController@product')->name('product');
});
