<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\IndexNewsletterSubscribersRequest;
use App\Http\Requests\Admin\Newsletter\StoreNewsletterSubscriberRequest;
use App\Http\Resources\Admin\Newsletter\NewsletterSubscriberDetailResource;
use App\Http\Resources\Admin\Newsletter\NewsletterSubscriberResource;
use App\Models\Newsletter\Subscriber;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\DB;

#[Group('Admin Newsletter Subs Management')]
class SubscriberController extends Controller
{
    /**
     * Display a listing of the subscribers.
     */
    public function index(IndexNewsletterSubscribersRequest $request)
    {
        $query = Subscriber::query()->select(['id', 'user_id', 'active', 'email', 'verified_at', 'created_at']);

        $query->when($request->has('email'), function ($query) use ($request) {
            $query->where('email', 'ilike', '%'.$request->email.'%');
        });

        $query->when($request->has('user_id'), function ($query) use ($request) {
            $query->where('user_id', $request->user_id);
        });

        $query->when($request->has('active'), function ($query) use ($request) {
            $query->where('active', $request->boolean('active'));
        });

        $query->when($request->has('verified'), function ($query) use ($request) {
            $request->boolean('verified') ? $query->whereNotNull('verified_at') : $query->whereNull('verified_at');
        });

        $subscribers = $query->latest()->paginate($request->integer('per_page', 25))->withQueryString();

        return NewsletterSubscriberResource::collection($subscribers);
    }

    /**
     * Display the specified subscriber.
     */
    public function show(Subscriber $subscriber)
    {
        $subscriber->load(['user', 'topics']);

        return new NewsletterSubscriberDetailResource($subscriber);
    }

    /**
     * Update the specified subscriber in storage.
     */
    public function update(StoreNewsletterSubscriberRequest $request, Subscriber $subscriber)
    {
        DB::transaction(function () use ($subscriber, $request) {
            $validated = $request->only(['email', 'active']);

            if ($request->filled('verified')) {
                $validated['verified_at'] = $request->boolean('verified') ? $subscriber->verified_at ?? now() : null;
            }

            $subscriber->update($validated);

            if ($request->has('topic_ids')) {
                $subscriber->topics()->sync($request->topic_ids ?? []);
            }
        });

        return response()->noContent();
    }

    /**
     * Remove the specified subscriber from storage.
     */
    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return response()->noContent();
    }
}
