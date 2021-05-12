<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Order;

class UpdateStatusFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_status:excel {file}';

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
            $successShippingCodes = [];
            $collectMoneyShippingCodes = [];
            foreach ($data as $key => $value) {
                if (!isset($value->so_hieu_buu_gui) || !(isset($value->trang_thai))) {
                    echo "Dòng $key thiếu thông tin số hiệu bưu gửi hoặc trạng thái";
                }
                
                if ($value->trang_thai == 1) {
                    $successShippingCodes[] = trim($value->so_hieu_buu_gui);
                }
                if ($value->trang_thai == 2) {
                    $code = trim($value->so_hieu_buu_gui);
                    $collectMoneyShippingCodes[] = $code;
                }
            }
            
            $a = Order::query()->whereIn('shipping_code', $successShippingCodes)->update([
                'status_id' => COMPLETE_ORDER_STATUS_ID
            ]);
            
            $b = Order::query()->whereIn('shipping_code', $collectMoneyShippingCodes)->update([
                'status_id' => COLLECT_MONEY_ORDER_STATUS_ID
            ]);
            
            DB::commit();
            
            echo 'Thanh cong ' . $a ;
            echo '\n';
            echo 'Thu tien ' . $b ;
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
