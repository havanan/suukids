<?php
/* User */
Route::group([
    'prefix' => 'user-group',
    'as' => 'user_group.'
], function () {
    Route::get('/', 'UserGroupController@index')->name('index');
    Route::post('save', 'UserGroupController@save')->name('save');
});
/* End User */

