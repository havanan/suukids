<?php

Route::group(['prefix' => 'login-log', 'as' => 'login_log.'], function () {
    Route::get('/', 'LoginLogController@index')->name('list');
    Route::get('/get-list', 'LoginLogController@getList')->name('get_list');
});
