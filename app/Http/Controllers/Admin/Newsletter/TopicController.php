<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsletterTopicRequest;
use App\Models\NewsletterTopic;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Gate;

#[Group('Admin Newsletter Topic Management')]
class TopicController extends Controller
{
    /**
     * Display a listing of the topics.
     */
    public function index()
    {
        Gate::authorize('read:newsletter-topic');

        return NewsletterTopic::get();
    }

    /**
     * Store a newly created topic in storage.
     */
    public function store(StoreNewsletterTopicRequest $request)
    {
        Gate::authorize('create:newsletter-topic');

        $validated = $request->validated();
        $topic = NewsletterTopic::create($validated);

        return response()->json(
            [
                'message' => 'Topic created.',
                'topic' => $topic,
            ],
            201,
        );
    }

    /**
     * Display the specified topic.
     */
    public function show(NewsletterTopic $topic)
    {
        Gate::authorize('read:newsletter-topic');

        return $topic;
    }

    /**
     * Update the specified topic in storage.
     */
    public function update(StoreNewsletterTopicRequest $request, NewsletterTopic $topic)
    {
        Gate::authorize('update:newsletter-topic');

        $validated = $request->validated();
        $topic->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified topic from storage.
     */
    public function destroy(NewsletterTopic $topic)
    {
        Gate::authorize('delete:newsletter-topic');

        $topic->delete();

        return response()->noContent();
    }
}
