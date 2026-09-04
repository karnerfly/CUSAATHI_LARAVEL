<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactMessage\IndexContactMessageRequest;
use App\Http\Requests\Admin\ContactMessage\MarkAsContactMessageRequest;
use App\Models\ContactMessage;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Contact Message')]
class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(IndexContactMessageRequest $request)
    {
        $query = ContactMessage::query();

        $query->when($request->filled('status'), fn($query) => $query->where('status', $request->status));
        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('subject', 'ilike', "%{$search}%");
            });
        });

        $messages = $query->latest()->paginate($request->integer('per_page', 25))->withQueryString();

        return response()->json($messages);
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $message)
    {
        return $message;
    }

    /**
     * Update the specified contact message in storage.
     */
    public function mark_as(MarkAsContactMessageRequest $request, ContactMessage $message)
    {
        $status = $request->input('status');
        $message->status = $status;
        $message->save();

        return response()->noContent();
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return response()->noContent();
    }
}
