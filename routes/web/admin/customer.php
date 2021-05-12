<?php
/* Customer */
Route::group([
    'prefix' => 'customer', 'middleware' => 'check-permission:manager_customer'
], function () {
  Route::get('/', 'CustomerController@index')->name('customer.index');
  Route::get('create', 'CustomerController@create')->name('customer.create');
  Route::get('overview', 'CustomerController@overview')->name('customer.overview');
  Route::post('overview-update', 'CustomerController@overviewUpdate')->name('customer.overviewUpdate');
  Route::post('overview-tag-add', 'CustomerController@overviewTagAdd')->name('customer.overviewTagAdd');
  Route::post('overview-tag-update', 'CustomerController@overviewTagUpdate')->name('customer.overviewTagUpdate');
  Route::get('get-list', 'CustomerController@getList')->name('customer.getList');
  Route::get('select-contact-user', 'CustomerController@sltContactUser')->name('customer.sltContactUser');
  Route::post('save-call', 'CustomerController@saveCallHistory')->name('customer.save.call');
  Route::post('save-note', 'CustomerController@saveNoteHistory')->name('customer.save.note');
  Route::post('history-call', 'CustomerController@historyCall')->name('customer.history.call');
  Route::post('history-note', 'CustomerController@historyNote')->name('customer.history.note');
  // Route::get('edit/{id?}', 'CustomerController@edit')->name('customer.edit');
  Route::post('create', 'CustomerController@store')->name('customer.store');
  Route::put('update/{id?}', 'CustomerController@update')->name('customer.update');
  Route::get('detail/{id?}', 'CustomerController@detail')->name('customer.detail');
  Route::delete('delete', 'CustomerController@delete')->name('customer.delete');

  Route::post('detail/note', 'CustomerController@detailNote')->name('customer.detail.note');
  Route::post('save/pathological', 'CustomerController@savePathological')->name('customer.save.pathological');
  Route::post('detail/call', 'CustomerController@detailCall')->name('customer.detail.call');
  Route::get('call/list/{id}', 'CustomerController@listDetailCall')->name('customer.detail.list.call');
  Route::get('note/list/{id}', 'CustomerController@listDetailNote')->name('customer.detail.list.note');
  Route::get('pathological/list/{id}', 'CustomerController@listDetailPathological')->name('customer.detail.list.pathological');
  Route::post('detail/pathological', 'CustomerController@detailPathological')->name('customer.detail.pathological');

  Route::any('/group/add', 'CustomerController@customerGroupAdd')->name('customer.group.add');
  Route::any('/group/list', 'CustomerController@customerGroupList')->name('customer.group.list');
  Route::post('/group/delete', 'CustomerController@customerGroupDelete')->name('customer.group.delete');

});
/* End Customer */
