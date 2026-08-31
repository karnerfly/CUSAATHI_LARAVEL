<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePermissionRequest;
use App\Http\Requests\Admin\GetPermissionsRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class AdminPermissionController extends Controller
{
    function create(CreatePermissionRequest $request)
    {
        $permission = Permission::create([
            'name' => $request->name,
            'ability' => $request->ability,
        ]);

        $permission->save();

        return response()->json(
            [
                'message' => 'permission created.',
                'permission' => $permission,
            ],
            201,
        );
    }

    public function get(GetPermissionsRequest $request)
    {
        $adminId = $request->input('admin_id');
        $query = Permission::query();
        if ($adminId) {
            $query->whereHas('admins', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            });
        }
        return $query->get();
    }
}
