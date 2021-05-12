<?php

Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
    Route::get('/change-password', 'ProfileController@changePassword')->name('change_password');
    Route::post('/change-password', 'ProfileController@changePasswordPost')->name('change_password');

});