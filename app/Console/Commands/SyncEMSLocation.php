<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EMSRepository;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use DB;

class SyncEMSLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ems:sync_location';

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
            echo 'Fetching Data from ' . env('TRANSPORT_BASE_URL') . "\n";
            $emsProvinces = $this->repository->getListProvince();
            $emsDistricts = $this->repository->getListDistrict();
            $emsWards = $this->repository->getListWard();
            echo 'Fetcted' . "\n";
            echo 'Insert Data to DB' . "\n";
            
            echo 'Syncing Provinces' . "\n";
            foreach ($emsProvinces as $emsProvince) {
               $province = new Province;
               $province->_name = $emsProvince->name;
               $province->ems_code = $emsProvince->code;
               $province->save();
            }
            
            echo 'Syncing Districts' . "\n";
            foreach ($emsDistricts as $emsDistrict) {
                $province = Province::query()->where('ems_code', $emsDistrict->province_code)->first();
                $district = new District;
                $district->_province_id = $province->id;
                $district->_name = $emsDistrict->name;
                $district->ems_code = $emsDistrict->code;
                $district->save();
            }
            
            echo 'Syncing Ward' . "\n";
            foreach ($emsWards as $emsWard) {
                $province = Province::query()->where('ems_code', $emsWard->province_code)->first();
                $district = District::query()->where('ems_code', $emsWard->district_code)->first();
                $ward = new Ward;
                $ward->_name = $emsWard->name;
                $ward->_district_id = $district->id;
                $ward->_province_id = $province->id;
                $ward->ems_code = $emsWard->code;
                $ward->save();
             }
            
            DB::commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
