<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EMSRepository;
use App\Models\EMSService;
use DB;

class SyncEMSServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ems:sync_services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $services = $this->repository->getListService();
        
            EMSService::query()->delete();
            foreach ($services as $service) {            
                EMSService::create([
                    'ems_code' => $service->code,
                    'name' => $service->name,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
