<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\StoreNewsletterTopicRequest;
use App\Models\Newsletter\Topic;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Newsletter Topic Management')]
class TopicController extends Controller
{
    /**
     * Display a listing of the topics.
     */
    public function index()
    {
        return Topic::get();
    }

    /**
     * Store a newly created topic in storage.
     */
    public function store(StoreNewsletterTopicRequest $request)
    {
        $validated = $request->validated();
        $topic = Topic::create($validated);

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
    public function show(Topic $topic)
    {
        return $topic;
    }

    /**
     * Update the specified topic in storage.
     */
    public function update(StoreNewsletterTopicRequest $request, Topic $topic)
    {
        $validated = $request->validated();
        $topic->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified topic from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();

        return response()->noContent();
    }
}
