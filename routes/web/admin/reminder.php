<?php

Route::group([
  'prefix' => 'reminder',
], function () {
  Route::get('index', 'ReminderController@index')->name('reminder.index');

});
