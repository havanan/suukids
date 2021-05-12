<?php

namespace App\Helpers;

use App\Models\ExportLog;
use App\Models\Shop;
use Log;

class ExportLogHelper
{
    public static function addLogExportExcel($title, $detail, $url, $ip)
    {
        try {
            $shopInfo = Shop::where([
                ['id', getCurrentUser()->shop_id],
            ])->firstOrFail();

            $logs = new ExportLog;
            $logs->title = $title;
            $logs->detail = $detail;
            $logs->url = $url;
            $logs->account_id = getCurrentUser()->id;
            $logs->user_name = getCurrentUser()->name;
            $logs->ip = $ip;
            $logs->shop_id = getCurrentUser()->shop_id;
            $logs->shop_name = $shopInfo->name;
            $logs->save();

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
