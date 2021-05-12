<?php

Route::group(['prefix' => 'hook-log', 'as' => 'hook_log.'], function () {
    Route::get('/', 'HookLogController@index');
});
