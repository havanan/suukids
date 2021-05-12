<?php

//EMS
Route::get('/stock/ems/config', 'StockEMSConfigController@index')->name('stock.ems_config');
Route::post('/stock/ems/config', 'StockEMSConfigController@store')->name('stock.ems_config.save');
Route::get('/stock/ems/inventory/list', 'StockEMSConfigController@getListInventory')->name('stock.ems.inventory.list');
// VTPOST

Route::get('/stock/vtpost/config', 'StockVTPostController@index')->name('stock.vtpost_config');
Route::post('/stock/vtpost/config', 'StockVTPostController@store')->name('stock.vtpost_config.save');
/* Stock In */
Route::group([
    'prefix' => 'stock-in', 'middleware' => 'check-permission:stock_in'
], function () {
    Route::any('/', 'StockInController@index')->name('stock.stock_in_list');
    Route::get('/create', 'StockInController@create')->name('stock.stock_in_import');
    Route::post('/store', 'StockInController@store')->name('stock.stock_in_store');
    Route::get('/edit/{id?}', 'StockInController@edit')->name('stock.stock_in_edit');
    Route::get('/get-product', 'StockInController@getProduct')->name('stock.stock_in_get_product');
    Route::post('/delete', 'StockInController@delete')->name('stock_in.delete');
    Route::post('/inportExcel', 'StockInController@inportExcel')->name('stock.stock_in.inportExcel');
    Route::get('/view/{id?}', 'StockInController@view')->name('stock.stock_in.view');
});
/* End Stock In */

/* Stock Out */
Route::group([
    'prefix' => 'stock-out', 'middleware' => 'check-permission:stock_out'
], function () {
    Route::any('/', 'StockOutController@index')->name('stock.stock_out_list');
    Route::get('/create', 'StockOutController@create')->name('stock.stock_out_import');
    Route::post('/store', 'StockOutController@store')->name('stock.stock_out_store');
    Route::get('/edit/{id?}', 'StockOutController@edit')->name('stock.stock_out_edit');
    Route::get('/get-product', 'StockOutController@getProduct')->name('stock.stock_out_get_product');
    Route::post('/delete', 'StockOutController@delete')->name('stock_out.delete');
    Route::post('/inportExcel', 'StockOutController@inportExcel')->name('stock.stock_out.inportExcel');
    Route::get('/move-product', 'StockOutController@moveProduct')->name('stock.stock_out_move_product');
    Route::get('/view/{id?}', 'StockOutController@view')->name('stock.stock_out.view');
});
/* End Stock Out */

/* supplier */
Route::group([
    'prefix' => 'supplier', 'middleware' => 'check-permission:define_supplier'
], function () {
    Route::get('/', 'SupplierController@index')->name('supplier.index');
    Route::post('save', 'SupplierController@save')->name('supplier.save');
});
/* End supplier */

/* Stock warehouse*/
Route::group([
    'prefix' => 'stock', 'middleware' => 'check-permission:define_warehouse'
], function () {
    Route::any('/product', 'StockController@product')->name('stock.product');
    Route::any('/warehouse/add', 'StockController@warehouseAdd')->name('stock.warehouse.add');
    Route::any('/warehouse/edit/{id}', 'StockController@warehouseEdit')->name('stock.warehouse.edit');
    Route::any('/warehouse/list', 'StockController@warehouseList')->name('stock.warehouse.list');
    Route::post('/warehouse/delete', 'StockController@warehouseDelete')->name('stock.warehouse.delete');

});
/* End Stock warehouse*/
