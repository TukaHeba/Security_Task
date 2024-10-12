<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    /**
     * Retrieve all roles with pagination.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllRoles()
    {
        try {
            return Role::with('permissions')->paginate(5);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new role with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return Role|\Illuminate\Database\Eloquent\Model
     */
    public function createRole(array $data, array $permissionIds)
    {
        try {
            $role = Role::create($data);
            $role->assignPermissions($permissionIds);
            $role->load('permissions');

            return $role;
        } catch (\Exception $e) {
            Log::error('Role creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single role.
     * 
     * @param string $id
     * @throws \Exception
     * @return Role
     */
    public function showRole(string $id)
    {
        try {
            return Role::with('permissions')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Role not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to retrieve role: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing role with the provided data.
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Role
     */
    public function updateRole(string $id, array $data, array $permissionIds = [])
    {
        try {
            $role = Role::findOrFail($id);
            $role->update(array_filter($data));

            // Filter valid permission IDs
            // Sync permissions only if non-empty array
            $validPermissionIds = array_filter($permissionIds, function ($permissionId) {
                return !is_null($permissionId);
            });

            if (!empty($validPermissionIds)) {
                $role->permissions()->sync($validPermissionIds);
            }
            $role->load('permissions');

            return $role;
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Role not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to update role: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a role.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteRole(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            return $role->delete();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Role not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Assign permissions to a role.
     * 
     * @param string $roleId
     * @param array $permissionIds
     * @throws \Exception
     * @return Role
     */
    public function assignPermissionsToRole(string $roleId, array $permissionIds)
    {
        try {
            $role = Role::findOrFail($roleId);  
            $role->assignPermissions($permissionIds); 
            $role->load('permissions'); 

            return $role;
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Role not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to assign permissions: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
