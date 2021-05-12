<?php

namespace App\Repositories\Admin\Profile;

use App\Models\Permission;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{
    /**
     * PermissionRepository constructor.
     *
     * @param  Permission  $model
     */
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model::query()->onlyCurrentShop()->get();
    }

    public function create($param)
    {

        return $this->model::query()->create($param);
    }

    public function update($id, $param)
    {
        return $this->model::query()->whereKey($id)->update($param);
    }

    public function deleteByIds($ids)
    {
        return $this->model::query()->whereIn('id', $ids)->delete();
    }

}