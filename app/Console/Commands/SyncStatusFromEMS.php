<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EMSRepository;
use App\Models\EMSOrderStatus;
use DB;

class SyncStatusFromEMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ems:sync_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ danh sách trạng thái từ ems';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    
    protected $repository;
     
    public function __construct(EMSRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $statuses = $this->repository->getListStatus();
        
            EMSOrderStatus::query()->delete();
            foreach ($statuses as $status) {
                $isComplete = ($status->name == 'Phát thành công') ? 1 : 0;
                $isRefund = ($status->name == 'Chuyển Hoàn') ? 1 : 0;
                // $isCollectMoney = ($status->name == 'Chuyển Hoàn') ? 1 : 0;
            
                EMSOrderStatus::create([
                    'code' => $status->code,
                    'name' => $status->name,
                    'is_complete' => $isComplete,
                    'is_refund' => $isRefund,
                    // 'is_collect_money' => $isCollectMoney,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
