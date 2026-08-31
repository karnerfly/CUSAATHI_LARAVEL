<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetPermissionsRequest;
use App\Http\Requests\Admin\ManagePermissionRequest;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Validation\ValidationException;

class AdminPermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(GetPermissionsRequest $request)
    {
        $admin_id = $request->input('admin_id');
        $query = Permission::query();
        if ($admin_id) {
            $query->whereHas('admins', function ($query) use ($admin_id) {
                $query->where('admin_id', $admin_id);
            });
        }

        return $query->get();
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->only(['name', 'ability']));

        return response()->json(
            [
                'message' => 'permission created.',
                'permission' => $permission,
            ],
            201,
        );
    }

    /**
     * Display the specified permission.
     */
    public function show(string $id)
    {
        return Permission::findOrFail($id);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(StorePermissionRequest $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->all(['name', 'ability']));

        return response()->json(
            [
                'message' => 'permission updated.',
                'permission' => $permission,
            ],
            200,
        );
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->noContent();
    }

    /**
     * Assign a permission to an admin.
     */
    public function assign(ManagePermissionRequest $request)
    {
        $admin_id = $request->input('admin_id');
        $permission_id = $request->input('permission_id');

        $admin = Admin::find($admin_id);
        if ($admin == null) {
            return response()->json(
                [
                    'message' => 'admin does not exists.',
                ],
                404,
            );
        }

        $permission = Permission::find($permission_id);
        if ($permission == null) {
            return response()->json(
                [
                    'message' => 'permission does not exists.',
                ],
                404,
            );
        }

        $permission = Permission::find($permission_id);
        if ($permission == null) {
            return response()->json(
                [
                    'message' => 'permission does not exists.',
                ],
                404,
            );
        }

        if ($admin->permissions()->where('id', $permission_id)->exists()) {
            throw ValidationException::withMessages([
                'permission_id' => 'permission already assigned to this admin.',
            ]);
        }

        $admin->permissions()->attach($permission_id);

        return response()->noContent();
    }

    /**
     * Revoke permission from an admin.
     */
    public function revoke(ManagePermissionRequest $request)
    {
        $admin_id = $request->input('admin_id');
        $permission_id = $request->input('permission_id');

        $admin = Admin::find($admin_id);
        if ($admin == null) {
            return response()->json(
                [
                    'message' => 'admin does not exists.',
                ],
                404,
            );
        }

        $permission = Permission::find($permission_id);
        if ($permission == null) {
            return response()->json(
                [
                    'message' => 'permission does not exists.',
                ],
                404,
            );
        }

        if (!$admin->permissions()->where('id', $permission_id)->exists()) {
            throw ValidationException::withMessages([
                'permission_id' => 'permission was not assigned to this admin.',
            ]);
        }

        $admin->permissions()->detach($permission_id);

        return response()->noContent();
    }
}
