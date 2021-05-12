<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class RemoveCloseDateIfNeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:remove_close_date_if_need';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Order::query()->whereNotIn('status_id', STATUS_DON_HANG_CHOT)->update([
                'close_date' => null,
                'close_user_id' => null,
            ]);
            
            echo 'success';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
