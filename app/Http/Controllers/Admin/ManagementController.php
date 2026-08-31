<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Resources\Admin\AdminResource;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Management')]
class ManagementController extends Controller
{
    /**
     * Get all admins
     */
    public function get_all_admins()
    {
        return Admin::withoutTrashed()->orderBy('id')->get();
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
                'message' => 'admin created.',
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
}
