<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreCollegeLocationRequest;
use App\Models\College\Location;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Management')]
class CollegeLocationController extends Controller
{
    /**
     * Display the specified college location.
     */
    public function show(Location $location)
    {
        return $location;
    }

    /**
     * Update the specified college location in storage.
     */
    public function update(StoreCollegeLocationRequest $request, Location $location)
    {
        $validated = $request->validated();
        $location->update($validated);

        return response()->noContent();
    }
}
