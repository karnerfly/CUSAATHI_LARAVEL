<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notice\IndexNoticeRequest;
use App\Http\Requests\Admin\Notice\StoreNoticeRequest;
use App\Models\Notice;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Notice Management')]
class NoticeController extends Controller
{
    /**
     * Display a listing of the college notices.
     */
    public function index(IndexNoticeRequest $request)
    {
        $query = Notice::query();

        $query->when(
            $request->has('deleted'),
            fn($query) => $request->boolean('deleted') ? $query->onlyTrashed() : $query->whereNull('deleted_at'),
        );
        $query->when($request->has('curriculum'), fn($query) => $query->where('curriculum', $request->curriculum));
        $query->when($request->has('category'), fn($query) => $query->where('category', $request->category));
        $query->when($request->has('semester'), fn($query) => $query->where('semester', $request->semester));
        $query->when($request->has('semester'), fn($query) => $query->where('semester', $request->semester));
        $query->when($request->has('from'), fn($query) => $query->whereDate('published_date', '>=', $request->from));
        $query->when($request->has('to'), fn($query) => $query->whereDate('published_date', '<=', $request->to));
        $query->orderBy('created_at', $request->string('order', 'desc'));

        $notices = $query->paginate($request->integer('per_page', 25))->withQueryString();

        return $notices;
    }

    /**
     * Store a newly created college notice in storage.
     */
    public function store(StoreNoticeRequest $request)
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
    public function update(StoreNoticeRequest $request, Notice $notice)
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
