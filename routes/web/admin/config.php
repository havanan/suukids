<?php

Route::group([
    'prefix' => 'config', 'namespace' => 'Config', 'middleware' => 'check-admin', 'as' => 'config.'
], function () {
    Route::get('/', 'ConfigController@emsIndex')->name('ems.index');
    Route::post('/ems', 'ConfigController@saveEms')->name('ems.save');

    Route::get('/ems-token', 'ConfigController@emsSaveTokenView')->name('ems.viewSaveToken');
    Route::post('/ems-token', 'ConfigController@saveTokenEMS')->name('ems.savetoken');

    Route::get('/vtp', 'ConfigController@vtpIndex')->name('vtp.index');
//  viettel post
    Route::get('/vtpost', 'ConfigController@vtpostAccountIndex')->name('vtpost.index');
    Route::post('/vtpost', 'ConfigController@vtpostAccountSave')->name('vtpost.save');

    Route::get('/vtpost-shop', 'ConfigController@vtpostShopIndex')->name('vtpost.shop.index');
    Route::post('/vtpost-shop', 'ConfigController@vtpostShopCreate')->name('vtpost.shop.save');
    Route::post('/vtpost-config', 'ConfigController@vtpostConfigSave')->name('vtpost.config.save');
//  end viettel post
    Route::get('/cloudfone', 'ConfigController@cloudfoneIndex')->name('cloudfone.index');
    Route::post('/cloudfone', 'ConfigController@saveCloudfone')->name('cloudfone.save');
});

