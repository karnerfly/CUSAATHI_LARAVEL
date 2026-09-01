<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionsRequest;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Resources\Admin\PermissionResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        $permission = Permission::create($request->only(['name', 'ability', 'category']));

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
        $permission->update($request->all(['name', 'ability', 'category']));

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
        $admin->permissions()->syncWithoutDetaching($permission_ids);

        return response()->noContent();
    }

    /**
     *  Get permissions of the specified admin.
     */
    public function get_admin_permissions(Admin $admin): AnonymousResourceCollection
    {
        return PermissionResource::collection($admin->permissions()->orderBy('category')->get());
    }

    /**
     * Revoke permission from an admin.
     */
    public function revoke(Admin $admin, Permission $permission)
    {
        $admin->permissions()->detach($permission->id);

        return response()->noContent();
    }
}
