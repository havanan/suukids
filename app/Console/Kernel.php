<?php

namespace App\Console;

use App\Console\Commands\ResyncLocation;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\ResetPriceForImportOrder::class,
        Commands\ResetIsOldCustomer::class,
        Commands\SyncEMSInventories::class,
        Commands\SyncStatusFromEMS::class,
        Commands\SyncEMSLocation::class,
        Commands\SyncEMSServices::class,
        // Commands\UpdateProvinceData::class,
        Commands\MoveOrder3005ToNewShop::class,
        Commands\RemoveCloseDateIfNeed::class,
        Commands\UpdateStatusFromExcel::class,
        Commands\UpdateStatusFromExcel2::class,
        Commands\SyncVTPostLocation::class,
        Commands\SyncVTPostService::class,
        Commands\ConvertAddress::class,
        Commands\ResyncLocation::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('vtpost:re-sync-location')
                  ->cron('0 3 * * SUN');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
