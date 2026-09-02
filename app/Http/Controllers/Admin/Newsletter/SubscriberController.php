<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexNewsletterSubscribersRequest;
use App\Http\Requests\Admin\StoreNewsletterSubscriberRequest;
use App\Http\Resources\Admin\NewsletterSubscriberDetailResource;
use App\Http\Resources\Admin\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

#[Group('Admin Newsletter Subs Management')]
class SubscriberController extends Controller
{
    /**
     * Display a listing of the subscribers.
     */
    public function index(IndexNewsletterSubscribersRequest $request)
    {
        Gate::authorize('read:newsletter-subscriber');

        $query = NewsletterSubscriber::query()->select([
            'id',
            'user_id',
            'active',
            'email',
            'verified_at',
            'created_at',
        ]);

        $query->when($request->filled('email'), function ($query) use ($request) {
            $query->where('email', 'ilike', '%' . $request->email . '%');
        });

        $query->when($request->filled('user_id'), function ($query) use ($request) {
            $query->where('user_id', $request->user_id);
        });

        $query->when($request->filled('active'), function ($query) use ($request) {
            $query->where('active', $request->boolean('active'));
        });

        $query->when($request->filled('verified'), function ($query) use ($request) {
            $request->boolean('verified') ? $query->whereNotNull('verified_at') : $query->whereNull('verified_at');
        });

        $subscribers = $query->latest()->paginate($request->integer('per_page', 25))->withQueryString();

        return NewsletterSubscriberResource::collection($subscribers);
    }

    /**
     * Display the specified subscriber.
     */
    public function show(NewsletterSubscriber $subscriber)
    {
        Gate::authorize('read:newsletter-subscriber');

        $subscriber->load(['user', 'topics']);
        return new NewsletterSubscriberDetailResource($subscriber);
    }

    /**
     * Update the specified subscriber in storage.
     */
    public function update(StoreNewsletterSubscriberRequest $request, NewsletterSubscriber $subscriber)
    {
        Gate::authorize('update:newsletter-subscriber');

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
    public function destroy(NewsletterSubscriber $subscriber)
    {
        Gate::authorize('delete:newsletter-subscriber');

        $subscriber->delete();

        return response()->noContent();
    }
}
