<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexNewsletterSubscribersRequest;
use App\Http\Resources\Admin\NewsletterSubscriberDetailResource;
use App\Http\Resources\Admin\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Admin Newsletter Subs Management')]
class SubscriberController extends Controller
{
    /**
     * Display a listing of the subscribers.
     */
    public function index(IndexNewsletterSubscribersRequest $request)
    {
        $query = NewsletterSubscriber::query()->select([
            'id',
            'user_id',
            'active',
            'email',
            'frequency',
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

        $query->when($request->filled('frequency'), function ($query) use ($request) {
            $query->where('frequency', $request->frequency);
        });

        $query->when($request->filled('verified'), function ($query) use ($request) {
            $request->boolean('verified') ? $query->whereNotNull('verified_at') : $query->whereNull('verified_at');
        });

        $subscribers = $query->latest()->paginate($request->integer('per_page', 25))->withQueryString();

        return NewsletterSubscriberResource::collection($subscribers);
    }

    // /**
    //  * Store a newly created subscriber in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified subscriber.
     */
    public function show(NewsletterSubscriber $subscriber)
    {
        $subscriber->load(['user', 'topics']);
        return new NewsletterSubscriberDetailResource($subscriber);
    }

    /**
     * Update the specified subscriber in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified subscriber from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
