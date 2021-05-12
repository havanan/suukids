<?php
/* Report */
Route::group([
    'prefix' => 'report',
    'as' => 'report.',
], function () {
    Route::group(['middleware:check-permission:view_report_sale'], function () {
        Route::any('employee-turnover', 'ReportController@employeeTurnover')->name('employee_turnover');
        Route::any('order-rate', 'ReportController@orderRate')->name('order_rate');
        Route::get('evaluation-customer-care', 'ReportController@evaluationCustomerCare')->name('evaluation_customer_care');
        Route::get('revenue-by-status', 'ReportController@revenueByStatus')->name('revenue_by_status');
        Route::any('aggregate-sale', 'ReportController@aggregateSale')->name('aggregate_sale');
        Route::any('warehouse-sale-number', 'ReportProcessingOrderController@warehouseSaleNumber')->name('warehouse_sale_number');
        Route::get('sale-percent-mkt-data', 'ReportController@salePercentByMktData')->name('sale_percent_mkt_data');
        Route::get('get-data-sale-percent-mkt-data', 'ReportController@getDatasalePercentByMktData')->name('get_data_sale_percent_mkt_data');
        Route::post('sale-mkt-alignment', 'ReportController@saleMktAlignment')->name('sale-mkt-alignment');
        Route::post('fill-mkt-cost', 'ReportController@fillMarketingCost')->name('fill-mkt-cost');

    });

    Route::group(['middleware:check-permission:view_report_marketing'], function () {
        Route::get('marketing-revenue', 'ReportController@marketingRevenue')->name('marketing_revenue');
        Route::get('marketing-by-source', 'ReportController@marketingBySource')->name('marketing_by_source');
        Route::post('marketing-by-source', 'ReportController@marketingBySource');
        Route::get('mkt-percent-sale-data', 'ReportController@mtkPercentBySaleData')->name('mkt_percent_sale_data');
        Route::get('get-data-mkt-percent-sale-data', 'ReportController@getDatamtkPercentBySaleData')->name('get_data_mkt_percent_sale_data');

    });
//    Route::get('/', 'ReportController@index')->name('index');

    Route::any('overview', 'ReportController@overview')->name('overview');
    Route::any('marketing-stage', 'ReportController@marketingStage')->name('marketing_stage');
    Route::post('update-marketer', 'ReportController@updateMarketer')->name('update_marketer');
    Route::post('update-mkt-cost', 'ReportController@updateMarketingCost');
    Route::post('update-init-cost', 'ReportController@updateInitialMarketingCost');
    Route::post('get-mkt-cost', 'ReportController@getMarketingCost');
    Route::post('marketers', 'ReportController@marketers')->name('marketers');
    Route::get('adv-money', 'ReportController@advMoney')->name('adv_money');
    Route::get('province', 'ReportController@province')->name('province');
    Route::get('delivery', 'ReportController@delivery')->name('delivery');
    Route::get('product', 'ReportController@product')->name('product');
    Route::get('product-revenue', 'ReportController@productRevenue')->name('product_revenue');

    Route::get('order-status', 'ReportController@orderStatus')->name('order_status');

    Route::get('change-status', 'ReportController@changeStatus')->name('change_status');

    Route::any('order-unprocessed', 'ReportProcessingOrderController@orderUnprocessed')->name('order_unprocessed');
    Route::any('employee-order', 'ReportProcessingOrderController@employeeOfOrder')->name('employee_order');
    Route::any('daily-turnover', 'ReportProcessingOrderController@dailyTurnover')->name('daily_turnover');
});
/* End Report */
