<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreCourseRequest;
use App\Http\Resources\Admin\College\CourseResource;
use App\Models\College\Course;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Meta Management')]
class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        return CourseResource::collection(Course::with('course_type')->get());
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();
        $course = Course::create($validated);

        return response()->json(
            [
                'message' => 'Course created.',
                'course' => $course,
            ],
            201,
        );
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load('course_type');

        return CourseResource::make($course);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(StoreCourseRequest $request, Course $course)
    {
        $validated = $request->validated();
        $course->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->noContent();
    }
}
