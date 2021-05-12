<?php

namespace App\Http\Controllers\Admin\Profile;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Profile\Permission\UpdatePermissionRequest;
use App\Repositories\Admin\Profile\OrderStatusRepository;
use App\Repositories\Admin\Profile\PermissionRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $repository;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * PermissionController constructor.
     * @param PermissionRepository $repository
     */
    public function __construct(PermissionRepository $repository,
                                OrderStatusRepository $orderStatusRepository)
    {
        $this->repository = $repository;
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * Return index view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $permissions = $this->repository->getAll();
        return view(VIEW_ADMIN_PROFILE_PERMISSION . 'index', compact('permissions'));
    }

    /**
     * Return create view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        $orderStatus = $this->orderStatusRepository->getStatusOrderByLevel();
        return view(VIEW_ADMIN_PROFILE_PERMISSION . 'create', compact('orderStatus'));
    }

    /**
     * Store Permission
     */
    public function store(StorePermissionRequest $request) {
        try {
            $data = $this->getSaveDataFromReqquest($request);
            $data['shop_id'] = getCurrentUser()->shop_id;
            $this->repository->create($data);

            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Tạo nhóm quyền thành công",
                "url" => route('admin.profile.permission.index'),
            ], HTTP_STATUS_SUCCESS);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->responseWithErrorMessage("Tạo thất bại, vui lòng thử lại sau.");
        }
    }

    public function edit(Request $request, $id) {
        $data = $this->repository->getById($id);
        $orderStatus = $this->orderStatusRepository->getStatusOrderByLevel();
        $statusPermissions = (array)json_decode($data->status_permissions);
        return view(VIEW_ADMIN_PROFILE_PERMISSION . 'edit', compact('data', 'orderStatus', 'statusPermissions'));
    }

    public function update(UpdatePermissionRequest $request, $id) {
        try {
            $this->repository->update($id, $this->getSaveDataFromReqquest($request));
            return response()->json([
                "code" => HTTP_STATUS_SUCCESS,
                "message" => "Cập nhật nhóm quyền thành công",
                "url" => route('admin.profile.permission.index'),
            ], HTTP_STATUS_SUCCESS);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->responseWithErrorMessage("Tạo thất bại, vui lòng thử lại sau.");
        }
    }

    private function getSaveDataFromReqquest(FormRequest $request) {
        $name = $request->get('name');

        $permissionData = [];
        foreach (PERMISSIONS_TITLE as $key => $value) {
            $permissionData[$key] = $request->get($key) ? 1 : 0;
        }

        $statuses = $this->orderStatusRepository->all();

        $statusData = [];

        foreach ($statuses->toArray() as $status) {
            $value = $request->get("status_" . $status['id']);
            if (!empty($value)) {
                $statusData[$status['id']] = intval($value);
            }
        }

        $param = array_merge([
            'name' => $name
        ], $permissionData, ['status_permissions' => json_encode($statusData)]);

        return $param;
    }

    public function delete(Request $request) {
        try {
            $ids = $request->get('data');
            $this->repository->deleteByIds($ids);
            return $this->statusOK();
        } catch(\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->responseWithErrorMessage("Có lỗi xảy ra, vui lòng thử lại sau");
        }
    }

}
