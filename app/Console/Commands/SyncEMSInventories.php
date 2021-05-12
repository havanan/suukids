<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EMSRepository;
use App\Models\EMSInventory;
use DB;

class SyncEMSInventories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ems:sync_inventories';

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
            $inventories = $this->repository->getListInventory();
        
            EMSInventory::query()->delete();
            foreach ($inventories as $inventory) {            
                EMSInventory::create([
                    'ems_id' => $inventory->id,
                    'name' => $inventory->name,
                    'username' => $inventory->username,
                    'address' => $inventory->address
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
