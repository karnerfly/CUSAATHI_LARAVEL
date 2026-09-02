<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\StoreNewsletterPreferenceRequest;
use App\Models\NewsletterSubscriber;
use Dedoc\Scramble\Attributes\Group;

#[Group('Newsletter')]
class NewsletterPreferenceController extends Controller
{
    /**
     * Get preferences of specified subscriber.
     */
    public function show(NewsletterSubscriber $subscriber)
    {
        return response()->json([
            'email' => $subscriber->email,
            'selected_topics' => $subscriber->topics()->pluck('id'),
        ]);
    }

    /**
     * Update preferences of specified subscriber.
     */
    public function update(StoreNewsletterPreferenceRequest $request, NewsletterSubscriber $subscriber)
    {
        if ($request->has('topic_ids')) {
            $subscriber->topics()->sync($request->topic_ids ?? []);
        }

        return response()->noContent();
    }
}
