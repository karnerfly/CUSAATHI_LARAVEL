<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreCourseTypeRequest;
use App\Models\College\CourseType;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Meta Management')]
class CourseTypeController extends Controller
{
    /**
     * Display a listing of the course types.
     */
    public function index()
    {
        return CourseType::get();
    }

    /**
     * Store a newly created course type in storage.
     */
    public function store(StoreCourseTypeRequest $request)
    {
        $validated = $request->validated();
        $type = CourseType::create($validated);

        return response()->json(
            [
                'message' => 'Course type created.',
                'course_type' => $type,
            ],
            201,
        );
    }

    /**
     * Display the specified course type.
     */
    public function show(CourseType $type)
    {
        return $type;
    }

    /**
     * Update the specified course type in storage.
     */
    public function update(StoreCourseTypeRequest $request, CourseType $type)
    {
        $validated = $request->validated();
        $type->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified course type from storage.
     */
    public function destroy(CourseType $type)
    {
        $type->delete();

        return response()->noContent();
    }
}
