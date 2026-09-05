<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreFacilityRequest;
use App\Models\College\Facility;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Meta Management')]
class FacilityController extends Controller
{
    /**
     * Display a listing of the facilities.
     */
    public function index()
    {
        return Facility::get();
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(StoreFacilityRequest $request)
    {
        $validated = $request->validated();
        $facility = Facility::create($validated);

        return response()->json(
            [
                'message' => 'Facility created.',
                'facility' => $facility,
            ],
            201,
        );
    }

    /**
     * Display the specified facility.
     */
    public function show(Facility $facility)
    {
        return $facility;
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(StoreFacilityRequest $request, Facility $facility)
    {
        $validated = $request->validated();
        $facility->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility)
    {
        $facility->delete();

        return response()->noContent();
    }
}
