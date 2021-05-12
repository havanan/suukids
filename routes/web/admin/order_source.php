<?php
/* Order Source */
Route::group([
    'prefix' => 'order-source',
    'middleware' => 'check-admin',
], function () {
    Route::get('/', 'OrderSourceController@index')->name('order_source.index');
    Route::post('delete', 'OrderSourceController@delete')->name('order_source.delete');
    Route::post('save', 'OrderSourceController@save')->name('order_source.save');
});
/* End Order Source */
