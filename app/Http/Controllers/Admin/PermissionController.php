<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionsRequest;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Resources\Admin\PermissionResource;
use App\Models\Admin;
use App\Models\Permission;
use Dedoc\Scramble\Attributes\Group;
use OwenIt\Auditing\Facades\Auditor;

#[Group('Admin Permission')]
class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        return Permission::orderBy('category')->get();
    }

    /**
     * Store a newly created permission.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        return response()->json(
            [
                'message' => 'Permission created.',
                'permission' => $permission,
            ],
            201,
        );
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        return $permission;
    }

    /**
     * Update the specified permission.
     */
    public function update(StorePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return response()->noContent();
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->noContent();
    }

    /**
     * Assign a permission to an admin.
     */
    public function assign(AssignPermissionsRequest $request, Admin $admin)
    {
        $permission_ids = $request->input('permission_ids');
        $changes = $admin->permissions()->syncWithoutDetaching($permission_ids);

        if (!empty($changes['attached'])) {
            $admin->auditEvent = 'permission_assigned';
            $admin->isCustomEvent = true;
            $admin->auditCustomOld = [];
            $admin->auditCustomNew = [
                'assigned_permission_ids' => $changes['attached'],
            ];

            Auditor::execute($admin);
        }

        return response()->noContent();
    }

    /**
     * Get permissions of the specified admin.
     * @response PermissionResource[]
     */
    public function get_admin_permissions(Admin $admin)
    {
        return PermissionResource::collection($admin->permissions()->orderBy('category')->get());
    }

    /**
     * Revoke permission from an admin.
     */
    public function revoke(Admin $admin, Permission $permission)
    {
        $detached_ids = $admin->permissions()->detach($permission->id);
        if ($detached_ids > 0) {
            $admin->auditEvent = 'permission_revoked';
            $admin->isCustomEvent = true;
            $admin->auditCustomOld = [
                'revoked_permission_id' => $permission->id,
            ];
            $admin->auditCustomNew = [];

            Auditor::execute($admin);
        }

        return response()->noContent();
    }
}
