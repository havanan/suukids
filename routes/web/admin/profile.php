<?php

Route::group([
    'prefix' => 'profile',
    'namespace' => 'Profile',
    'as' => 'profile.',
    'middleware' => 'check-admin'
], function () {
    /** Permission  */
    Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {
        Route::get('/', 'PermissionController@index')->name('index');
        Route::get('/create', 'PermissionController@create')->name('create');
        Route::post('/create', 'PermissionController@store')->name('store');
        Route::get('/edit/{id}', 'PermissionController@edit')->name('edit');
        Route::post('/edit/{id}', 'PermissionController@update')->name('update');
        Route::delete('/delete', 'PermissionController@delete')->name('delete');
    });
});
