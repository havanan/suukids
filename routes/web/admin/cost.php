<?php
/* Customer */
Route::group([
    'prefix' => 'cost',
    'as' => 'cost.',
    'middleware' => 'check-admin'
], function () {
  Route::get('/', 'CostController@index');

});
/* End Customer */
