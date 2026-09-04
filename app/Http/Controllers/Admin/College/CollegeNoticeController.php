<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreCollegeNoticeRequest;
use App\Models\College\Notice;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Notice Management')]
class CollegeNoticeController extends Controller
{
    /**
     * Display a listing of the college notices.
     */
    public function index()
    {
        return Notice::withTrashed()->get();
    }

    /**
     * Store a newly created college notice in storage.
     */
    public function store(StoreCollegeNoticeRequest $request)
    {
        $validated = $request->validated();
        $notice = Notice::create($validated);

        return response()->json(
            [
                'message' => 'College notice created.',
                'college_notice' => $notice,
            ],
            201,
        );
    }

    /**
     * Display the specified college notice.
     */
    public function show(Notice $notice)
    {
        return $notice;
    }

    /**
     * Update the specified college notice in storage.
     */
    public function update(StoreCollegeNoticeRequest $request, Notice $notice)
    {
        $validated = $request->validated();
        $notice->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified college notice from storage.
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified college notice from storage.
     */
    public function restore(Notice $notice)
    {
        $notice->restore();

        return response()->noContent();
    }
}
