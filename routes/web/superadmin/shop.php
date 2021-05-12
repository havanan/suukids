<?php

Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
    Route::get('/', 'ShopController@index')->name('index');
    Route::get('/create', 'ShopController@create')->name('create');
    Route::post('/create', 'ShopController@store')->name('store');
    Route::get('/edit/{id}', 'ShopController@edit')->name('edit');
    Route::put('/edit/{id}', 'ShopController@update')->name('update');
    Route::get('/login/{id}', 'ShopController@shopLogin')->name('shopLogin');
    Route::post('/delete-shop', 'ShopController@deleteShop')->name('delete');

});