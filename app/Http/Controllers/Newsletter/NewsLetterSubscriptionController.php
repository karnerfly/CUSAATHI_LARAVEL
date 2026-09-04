<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\NewsletterSubscribeRequest;
use App\Models\Newsletter\Subscriber;
use App\Models\Newsletter\Topic;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Str;

#[Group('Newsletter')]
class NewsLetterSubscriptionController extends Controller
{
    /**
     * Display a listing of the newsletter topics.
     */
    public function topics()
    {
        return Topic::get();
    }

    /**
     * Subscribe in specified topics.
     */
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        $validated = $request->validated();

        $subscriber = Subscriber::firstOrNew(['email' => $validated['email']]);

        if ($subscriber->active && $subscriber->verified_at) {
            return response()->json(
                [
                    'message' => 'You are already subscribed to the newsletter.',
                ],
                409,
            );
        }

        $subscriber->unsubscribe_token = Str::random(48);
        $subscriber->verification_token = Str::random(48);
        $subscriber->active = false;
        $subscriber->user_id = $request->user()?->id;
        $subscriber->save();

        if ($request->has('topic_ids')) {
            $subscriber->topics()->sync($request->topic_ids ?? []);
        }

        $subscriber->sendVerificationMail($subscriber->verification_token);

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
        $subscriber = Subscriber::where('verification_token', $token)->firstOrFail();

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
    public function subscription(Subscriber $subscriber)
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
    public function unsubscribe(Subscriber $subscriber)
    {
        $subscriber->update(['active' => false]);

        return response()->noContent();
    }
}
