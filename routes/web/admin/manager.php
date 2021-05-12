<?php

/* Manager status, delivery */
Route::group([
  'prefix' => 'manager',
], function () {
  Route::any('/status', 'ManagerController@status')->name('status.index');
  Route::any('/delivery', 'ManagerController@delivery')->name('delivery.index');
  Route::any('/shop', 'ManagerController@shopInfo')->name('shop.index');

});
/* End Managet */