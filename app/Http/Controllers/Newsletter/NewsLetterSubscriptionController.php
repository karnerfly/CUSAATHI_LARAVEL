<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use App\Models\NewsLetterTopic;
use Illuminate\Support\Str;

class NewsLetterSubscriptionController extends Controller
{
    /**
     * Display a listing of the newsletter topics.
     */
    public function topics()
    {
        return NewsLetterTopic::get();
    }

    /**
     * Subscribe in specified topics.
     */
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        $validated = $request->validated();

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $validated['email']]);

        $subscriber->frequency = $validated['frequency'];
        $subscriber->unsubscribe_token = Str::random(48);
        $subscriber->active = false;
        $subscriber->user_id = $request->user()?->id;
        $subscriber->save();

        $subscriber->topics()->sync($validated['topic_ids']);

        // TODO: Dispatch verification notification containing a frontend route like:
        // https://frontend.app/newsletter/verify?token=XYZ
        // Which subsequently calls POST /api/v1/newsletter/verify/XYZ

        return response()->json(
            [
                'status' => 'pending_verification',
            ],
            201,
        );
    }

    /**
     * Verify subscription email.
     */
    public function verify(string $token)
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)->firstOrFail();

        $subscriber->update([
            'verified_at' => now(),
            'active' => true,
            'verification_token' => null,
        ]);

        return response()->noContent();
    }

    /**
     * Get subscription status of specified subscriber.
     */
    public function subscription(NewsletterSubscriber $subscriber)
    {
        return response()->json([
            'email' => $subscriber->email,
            'active' => $subscriber->active,
            'subscribed_at' => $subscriber->created_at,
        ]);
    }

    /**
     * Unsubscribe from newsletters.
     */
    public function unsubscribe(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['active' => false]);

        return response()->noContent();
    }
}
