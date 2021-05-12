<?php

Route::group(['prefix' => 'action-log', 'as' => 'action_log.'], function () {
    Route::get('/', 'ActionLogController@index')->name('list');
    Route::get('/get-list', 'ActionLogController@getList')->name('get_list');
});
