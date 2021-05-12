<?php
/* Product */
Route::group([
    'prefix' => 'product', 'middleware' => 'check-admin'
], function () {
    Route::get('/', 'ProductController@index')->name('product.index');
    Route::get('create', 'ProductController@create')->name('product.create');
    Route::get('edit/{id}', 'ProductController@edit')->name('product.edit');
    Route::get('get-list', 'ProductController@getList')->name('product.getList');
    Route::get('export-excel', 'ProductController@exportExcel')->name('product.exportExcel');

    Route::get('delete/{id}', 'ProductController@destroy')->name('product.delete');
    Route::post('store', 'ProductController@store')->name('product.store');
    Route::post('import-excel', 'ProductController@importExcel')->name('product.importExcel');

    Route::put('update/{id}', 'ProductController@update')->name('product.update');

    /* Product bundle */
    Route::group([
        'prefix' => 'bundle',
    ], function () {
        Route::get('/', 'ProductBundleController@index')->name('bundle.index');
        Route::post('delete', 'ProductBundleController@delete')->name('bundle.delete');
        Route::post('save', 'ProductBundleController@save')->name('bundle.save');
    });
    /* End Product bundle */

    /* Product unit */
    Route::group([
        'prefix' => 'unit',
    ], function () {
        Route::get('/', 'ProductUnitController@index')->name('unit.index');
        Route::post('delete', 'ProductUnitController@delete')->name('unit.delete');
        Route::post('save', 'ProductUnitController@save')->name('unit.save');
    });
    /* End Product unit */

    /* Manager Product */
    Route::group([
        'prefix' => 'manager',
    ], function () {
        Route::get('', 'ProductManagerController@index')->name('manager.products');
        Route::get('/exportExcel', 'ProductManagerController@exportExcel')->name('manager.exportExcel');
        Route::post('/delete', 'ProductManagerController@delete')->name('manager.products.delete');
        Route::post('/store', 'ProductManagerController@store')->name('manager.products.store');
        Route::post('/validateBeforeSave', 'ProductManagerController@validateBeforeSave')->name('manager.products.validateBeforeSave');
    });
    /* End Manager Product */

});
/* End Product */
