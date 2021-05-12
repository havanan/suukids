<?php


namespace App\Console\Commands;


use App\Helpers\Common;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use App\Repositories\VTPostRepository;
use Illuminate\Console\Command;
use DB;
use function GuzzleHttp\Psr7\str;

class SyncVTPostLocation extends Command
{
    protected $signature = 'vtpost:sync_location';

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
            // sync list province from viettel post api
            $listProvinces = $this->repository->getListProvinceByCityCode();
            foreach ($listProvinces as $vtProvince) {
                $provine = new Province();
                $provine->_name = $vtProvince->PROVINCE_NAME;
                $provine->vtpost_province_id = $vtProvince->PROVINCE_ID;
                $provine->vtpost_province_code = $vtProvince->PROVINCE_CODE;
                $provine->save();
            }

            // sync list district from viettel post api
            $dListProvinces = DB::select('SELECT * FROM province WHERE vtpost_province_id IS NOT NULL');
            foreach ($dListProvinces as $dProvince) {
                $listDistricts = $this->repository->getListDistrictByProvinceId($dProvince->vtpost_province_id);
                foreach ($listDistricts as $district) {
                    $district_obj = new District();
                    $district_obj->_name = $district->DISTRICT_NAME;
                    $district_obj->_province_id = $dProvince->id;
                    $district_obj->vtpost_district_id = $district->DISTRICT_ID;
                    $district_obj->vtpost_district_value = $district->DISTRICT_VALUE;

                    $district_obj->save();
                }
            }

            // sync list ward from viettel post api
            $dListDistricts = DB::select('SELECT * FROM district WHERE vtpost_district_id IS NOT NULL');
            foreach ($dListDistricts as $dDistrict) {
                $listWards = $this->repository->getListWardByDistrictId($dDistrict->vtpost_district_id);
                foreach ($listWards as $ward) {
                    $ward_obj = new Ward();
                    $ward_obj->_name = $ward->WARDS_NAME;
                    $ward_obj->vtpost_ward_id = $ward->WARDS_ID;
                    $ward_obj->_district_id = $dDistrict->id;
                    $ward_obj->_province_id = $dDistrict->_province_id;

                    $ward_obj->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
