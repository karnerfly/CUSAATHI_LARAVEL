<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    function get_current_admin(Request $request)
    {
        return new AdminResource($request->user('admin'));
    }
}
