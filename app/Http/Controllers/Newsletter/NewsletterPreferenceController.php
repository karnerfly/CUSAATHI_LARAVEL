<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\UpdateNewsletterPreferenceRequest;
use App\Models\NewsletterSubscriber;

class NewsletterPreferenceController extends Controller
{
    /**
     * Get preferences of specified subscriber.
     */
    public function show(NewsletterSubscriber $subscriber)
    {
        return response()->json([
            'email' => $subscriber->email,
            'frequency' => $subscriber->frequency,
            'selected_topics' => $subscriber->topics()->pluck('id'),
        ]);
    }

    /**
     * Update preferences of specified subscriber.
     */
    public function update(UpdateNewsletterPreferenceRequest $request, NewsletterSubscriber $subscriber)
    {
        $subscriber->update([
            'frequency' => $request->input('frequency'),
        ]);

        $subscriber->topics()->sync($request->input('topic_ids'));

        return response()->noContent();
    }
}
