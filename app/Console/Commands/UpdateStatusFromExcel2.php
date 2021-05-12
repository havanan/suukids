<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Order;

class UpdateStatusFromExcel2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_status:excel2 {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status from excel by Shipping id';

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
        $path = $this->argument('file');
        DB::beginTransaction();
        try {
            $data = \Excel::load($path, function ($reader) {
                $reader->ignoreEmpty();
            })->get();
            $headerRow = $data->first()->keys()->toArray();
            $countRows = $data->count();
            
            echo 'Updateting ' . $countRows;
            echo "\n";
            $codes = [];
            foreach ($data as $key => $value) {
                if (!isset($value->ma)) {
                    echo "Dòng $key thiếu thông tin số mã vận đơn";
                    die;
                }
                
                $codes[] = trim($value->ma);
            }
            
            $a = Order::query()->whereIn('code', $codes)->update([
                'status_id' => DELIVERY_ORDER_STATUS_ID
            ]);
            
            DB::commit();
            echo 'Thanh cong ' . $a ;
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
