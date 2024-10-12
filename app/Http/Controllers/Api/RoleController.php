<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Services\ApiResponseService;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = $this->roleService->listAllRoles();
            return ApiResponseService::success(RoleResource::collection($roles), 'Roles retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        try {
            $newRole = $this->roleService->createRole($validated, $validated['permissions']);
            return ApiResponseService::success(new RoleResource($newRole), 'Role created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = $this->roleService->showRole($id);
            return ApiResponseService::success(new RoleResource($role), 'Role retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedRole = $this->roleService->updateRole($id, $validated, $validated['permissions'] ?? []);
            return ApiResponseService::success(new RoleResource($updatedRole), 'Role updated successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->roleService->deleteRole($id);
            return ApiResponseService::success(null, 'Role deleted successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }

    /**
     * Assign permissions to a role.
     */
    /**
     * Assign permissions to a role.
     * 
     * @param string $roleId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissions(string $roleId, Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $updatedRole = $this->roleService->assignPermissionsToRole($roleId, $validated['permissions']);
            return ApiResponseService::success(new RoleResource($updatedRole), 'Permissions assigned successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error('An error occurred on the server.', 500);
        }
    }
}
