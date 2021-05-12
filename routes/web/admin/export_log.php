<?php
Route::group([
    'prefix' => 'export-logs',
    'as' => 'export_logs.',
    'middleware' => 'check-admin'
], function () {
    Route::get('export-excel', 'ExportLogsController@excel')->name('excel');
});
