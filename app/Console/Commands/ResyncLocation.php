<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use App\Repositories\VTPostRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResyncLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vtpost:re-sync-location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resync location from viettel post';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VTPostRepository $repository)
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
            Province::query()->whereNotNull('vtpost_province_id')
                ->update([
                    'status' => 1
                ]);
            District::query()->whereNotNull('vtpost_district_id')
                ->update([
                    'status' => 1
                ]);
            Ward::query()->whereNotNull('vtpost_ward_id')
                ->update([
                    'status' => 1
                ]);

            $listProvince = $this->repository->getListProvinceByCityCode();

            $barProvince = $this->output->createProgressBar(count($listProvince));
            $barProvince->start();
            $listProvinceId = [];
            foreach ($listProvince as $province) {
                $listProvinceId[] = $province->PROVINCE_ID;
                $dProvinceCount = Province::query()->where('vtpost_province_id', $province->PROVINCE_ID)->count();
                if ($dProvinceCount < 1) {
                    $province_obj = new Province();
                    $province_obj->_name = $province->PROVINCE_NAME;
                    $province_obj->vtpost_province_id = $province->PROVINCE_ID;
                    $province_obj->vtpost_province_code = $province->PROVINCE_CODE;
                    $province_obj->province_slug = str_slug($province->PROVINCE_NAME);
                    $province_obj->save();
                }
                $barProvince->advance();
            }
            $result = Province::query()->whereNotNull('vtpost_province_id')
                ->whereNotIn('vtpost_province_id', $listProvinceId)
                ->update([
                    'status' => 0
                ]);
            $barProvince->finish();
            $this->output->newLine();
            $this->output->writeln('Province re-sync done!');

            $dListProvince = Province::query()->whereNotNull('vtpost_province_id')
                ->where('status', 1)
                ->get();

            $barDProvince = $this->output->createProgressBar($dListProvince->count());
            $barDProvince->start();
            $listDistrictIds = [];
            foreach ($dListProvince as $dProvince) {
                $listDistricts = $this->repository->getListDistrictByProvinceId($dProvince->vtpost_province_id);

                $barVTDistrict = $this->output->createProgressBar(count($listDistricts));
                $barVTDistrict->start();
                foreach ($listDistricts as $district) {

                    $listDistrictIds[] = $district->DISTRICT_ID;
                    $dDistrictCount = District::query()->where('vtpost_district_id', $district->DISTRICT_ID)->count();
                    if ($dDistrictCount < 1) {
                        $district_obj = new District();
                        $district_obj->_name = $district->DISTRICT_NAME;
                        $district_obj->_province_id = $dProvince->id;
                        $district_obj->vtpost_district_id = $district->DISTRICT_ID;
                        $district_obj->vtpost_district_value = $district->DISTRICT_VALUE;
                        $district_obj->district_slug  = str_slug($district->DISTRICT_NAME);
                        $district_obj->save();
                    }
                    $barVTDistrict->advance();
                }
                $barVTDistrict->finish();
                $this->output->newLine();
                $barDProvince->advance();
            }
            $resultDistrict = District::query()->whereNotNull('vtpost_district_id')
                ->whereNotIn('vtpost_district_id', $listDistrictIds)
                ->update([
                    'status' => 0
                ]);
            $barDProvince->finish();
            $this->output->newLine();
            $this->output->writeln('District re-sync done!');

            $dListDistricts = District::query()->whereNotNull('vtpost_district_id')
                ->where('status', 1)
                ->get();

            $barDDistrict = $this->output->createProgressBar($dListDistricts->count());
            $barDDistrict->start();
            $listWardIds  = [];
            foreach ($dListDistricts as $dDistrict) {
                $listWards = $this->repository->getListWardByDistrictId($dDistrict->vtpost_district_id);

                $barVtWard = $this->output->createProgressBar(count($listWards));
                $barVtWard->start();
                foreach ($listWards as $ward) {

                    $listWardIds[] = $ward->WARDS_ID;
                    $dWardCount = Ward::query()->where('vtpost_ward_id', $ward->WARDS_ID)->count();
                    if ($dWardCount < 1) {
                        $ward_obj = new Ward();
                        $ward_obj->_name = $ward->WARDS_NAME;
                        $ward_obj->vtpost_ward_id = $ward->WARDS_ID;
                        $ward_obj->_district_id = $dDistrict->id;
                        $ward_obj->_province_id = $dDistrict->_province_id;
                        $ward_obj->ward_slug = str_slug($ward->WARDS_NAME);
                        $ward_obj->save();
                    }
                    $barVtWard->advance();
                }
                $barVtWard->finish();
                $this->output->newLine();
                $barDDistrict->advance();
            }
            $resultWard = Ward::query()->whereNotNull('vtpost_ward_id')
                ->whereNotIn('vtpost_ward_id', $listWardIds)
                ->update([
                    'status' => 0
                ]);
            $barDDistrict->finish();
            $this->output->newLine();
            $this->output->writeln('Ward re-sync done!');
            DB::commit();
            Log::info('Re-Sync Location:: success');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::info('Re-Sync Location:: failed');
            echo $exception->getMessage();
        }
    }
}
