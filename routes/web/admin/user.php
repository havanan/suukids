<?php
/* User */
Route::group([
    'prefix' => 'user',
    'as' => 'user.',
], function () {
    Route::group(['middleware' => 'check-admin'], function () {
        Route::get('/', 'UserController@index')->name('index');

        Route::get('create', 'UserController@create')->name('create');
        Route::get('edit/{id}', 'UserController@edit')->name('edit');
        Route::get('destroy', 'UserController@destroy')->name('destroy');

        Route::get('get-list', 'UserController@getList')->name('getList');
        Route::post('save', 'UserController@save')->name('save');
        Route::post('update-member/{id}', 'UserController@updateMember')->name('updateMember');
    });

    Route::get('profile', 'UserController@profile')->name('profile');
    Route::post('update-pass', 'UserController@updatePass')->name('updatePass');
    Route::post('update/{id}', 'UserController@update')->name('update');
});
/* End User */