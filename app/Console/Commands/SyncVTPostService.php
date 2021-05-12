<?php


namespace App\Console\Commands;


use App\Helpers\Common;
use App\Models\District;
use App\Models\Province;
use App\Models\VTPOSTService;
use App\Models\VTPOSTStore;
use App\Models\Ward;
use App\Repositories\VTPostRepository;
use Illuminate\Console\Command;
use DB;

class SyncVTPostService extends Command
{
    protected $signature = 'vtpost:sync_service';

    protected $description = 'Command description';

    protected $repository;

    public function __construct(VTPostRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $listServices = $this->repository->getListServices();
            foreach ($listServices as $service) {
                $vtService = new VTPOSTService();
                $vtService->service_code = $service->SERVICE_CODE;
                $vtService->name = $service->SERVICE_NAME;

                $vtService->save();
            }

            $listStores = $this->repository->getListStore();
            foreach ($listStores as $store) {
                $vtStore = new VTPOSTStore();
                $vtStore->group_address_id = $store->groupaddressId;
                $vtStore->customer_id = $store->cusId;
                $vtStore->name = $store->name;
                $vtStore->phone = $store->phone;
                $vtStore->address = $store->address;
                $vtStore->province_id = $store->provinceId;
                $vtStore->district_id = $store->districtId;
                $vtStore->ward_id = $store->wardsId;

                $vtStore->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
