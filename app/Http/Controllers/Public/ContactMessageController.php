<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Dedoc\Scramble\Attributes\Group;

#[Group('Public Contact Message')]
class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message in storage.
     */
    public function store(StoreContactMessageRequest $request)
    {
        $validated = $request->validated();
        $contact_message = ContactMessage::create([...$validated, 'user_id' => $request->user()?->id]);

        return response()->json(
            [
                'message' => 'Contact message created.',
                'contact_message' => $contact_message,
            ],
            201,
        );
    }
}
