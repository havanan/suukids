<?php

Route::group(['prefix' => 'sell', 'namespace' => 'Sell', 'as' => 'sell.'], function () {
    /** Permission  */
    Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
        Route::get('/get-list-by-ids', 'OrderController@getOrdersByIds')->name('getListByIds');
        Route::get('/', 'OrderController@index')->name('index');
        Route::get('/get-list', 'OrderController@getList')->name('getList');
        Route::get('/get-info', 'OrderController@getInfo')->name('getInfo');

        Route::get('/create', 'OrderController@create')->name('create');
        Route::post('/create', 'OrderController@store')->name('store');
        // Route::post('/report-error', 'OrderController@reportError')->name('report-error');

        Route::group(['middleware' => 'check-permission:edit_order'], function () {
            Route::get('/edit/{id?}', 'OrderController@edit')->name('edit');
            Route::post('/edit/{id}', 'OrderController@update')->name('update');
        });

        Route::post('/status/update', 'OrderController@updateStatus')->name('update-status');
        Route::group(['middleware' => 'check-permission:share_orders'], function () {
            Route::post('/flash-share', 'OrderController@flashShare')->name('flash-share');
            Route::get('/count-not-assign', 'OrderController@countNotAssignOrder')->name('count-not-assign');
            Route::post('/assign-for-sale', 'OrderController@assignForSale')->name('assign-for-sale');
            Route::post('/assign-for-mkt', 'OrderController@assignForMarketing')->name('assign-for-mkt');
            Route::post('/assign-for-group', 'OrderController@assignForGroup')->name('assign-for-group');
        });
        Route::get('/search-by-phone', 'OrderController@searchByPhone')->name('search-by-phone');
        Route::get('/search-by-customer-id', 'OrderController@searchByCustomerId')->name('search-by-customer-id');
        Route::group(['middleware' => 'check-permission:export_excel'], function () {
            Route::get('/export-excel', 'OrderController@exportExcel')->name('export-excel');
        });

        Route::group(['middleware' => 'check-permission:quick_edit'], function () {
            Route::get('quick-edit', 'OrderController@quickEdit')->name('quickEdit');
            Route::post('quick-edit', 'OrderController@quickEdit')->name('quickEdit');
        });
        Route::post('flash-edit', 'OrderController@flashEdit')->name('flash-edit');

        Route::group(['middleware' => 'check-permission:import_excel'], function () {
            Route::get('import-excel', 'OrderController@importExcel')->name('importExcel');
            Route::post('upload-excel', 'OrderController@uploadImportExcel')->name('uploadImportExcel');
            Route::post('import-excel', 'OrderController@postImportExcel')->name('importExcel.post');
        });
        Route::group(['middleware' => 'check-permission:import_billWay'], function () {
            Route::get('import-excel-billway', 'OrderController@importExcelBillWay')->name('importExcelBillWay');
            Route::post('import-excel-billway', 'OrderController@postImportExcelBillWay')->name('importExcelBillWay.post');
            Route::get('import-excel-collect-money', 'OrderController@importExcelCollectMoney')->name('importExcelCollectMoney');
            Route::post('import-excel-collect-money', 'OrderController@postImportExcelCollectMoney')->name('importExcelCollectMoney.post');
        });

        Route::group(['middleware' => 'check-permission:delete_orders'], function () {
            Route::post('/delete-orders', 'OrderController@deleteOrders')->name('delete-orders');
        });

        Route::get('/common-search', 'OrderController@commonSearch')->name('commonSearch');

        Route::get('/waiting_orders', 'OrderController@getWaitingOrders')->name('waiting_orders');
        Route::post('/save-order-sort', 'OrderController@saveOrderSort')->name('saveOrderSort');
        Route::get('/revenue', 'OrderController@getTotallRevenue')->name('revenue');

        Route::get('/get-order-history', 'OrderController@getOrderHistory')->name('getOrderHistory');
        Route::get('take-care-again', 'TakeCareAgainController@index')->name('take-care-again');
        Route::get('take-care-again/{id}/called', 'TakeCareAgainController@updateCalled')->name('take-care-again/called');

        Route::post('call-cloudfone', 'OrderCloudfoneController@sendCallToCloudfone')->name('call-cloudfone');
        Route::get('call-history-cloudfone', 'OrderCloudfoneController@historyCloudfoneIndex')->name('call-history-cloudfone');
    });

    Route::get('product/search', 'ProductController@apiSearch')->name('product.api-search');
    Route::get('product/on-hand-info', 'ProductController@apiGetOnHandInfo')->name('product.api-on-hand-info');
});
