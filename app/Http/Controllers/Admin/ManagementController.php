<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Management')]
class ManagementController extends Controller
{
    /**
     * Get all admins
     */
    public function get_all_admins()
    {
        return Admin::withTrashed()->orderBy('id')->get();
    }

    /**
     * Store a newly created admin.
     */
    public function create_admin(StoreAdminRequest $request)
    {
        $admin = Admin::create($request->only(['name', 'email', 'password']));
        $admin->refresh();

        return response()->json(
            [
                'message' => 'Admin created.',
                'admin' => $admin,
            ],
            201,
        );
    }

    /**
     * Deactivate specified admin.
     */
    public function deactivate_admin(Admin $admin)
    {
        $admin->update(['active' => false]);

        return response()->noContent();
    }

    /**
     * Activate specified admin.
     */
    public function activate_admin(Admin $admin)
    {
        $admin->update(['active' => true]);

        return response()->noContent();
    }

    /**
     * Soft Delete specified admin.
     */
    public function delete_admin(Admin $admin)
    {
        $admin->delete();

        return response()->noContent();
    }

    /**
     * Restore specified admin.
     */
    public function restore_admin(Admin $admin)
    {
        $admin->restore();

        return response()->noContent();
    }
}
