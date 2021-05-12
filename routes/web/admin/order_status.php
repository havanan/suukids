<?php
/* Order Source */
Route::group([
    'prefix' => 'order-status',
    'middleware' => 'check-admin',
], function () {
    Route::post('update/{id}', 'OrderStatusController@update')->name('order_status.update');
});
/* End Order Source */
