<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexCampaignRequest;
use App\Http\Requests\Admin\StoreCampaignRequest;
use App\Jobs\NewsletterCampaignJob;
use App\Models\Newsletter;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Gate;

#[Group('Admin Newsletter Campaign Management')]
class CampaignController extends Controller
{
    /**
     * Display a listing of the campaigns.
     */
    public function index(IndexCampaignRequest $request)
    {
        Gate::authorize('read:newsletter');

        $query = Newsletter::with('topic:id,name,slug')->latest();

        if ($request->filled('status')) {
            match ($request->query('status')) {
                'sent' => $query->whereNotNull('sent_at'),
                'scheduled' => $query->whereNull('sent_at')->whereNotNull('scheduled_for'),
                'draft' => $query->whereNull('sent_at')->whereNull('scheduled_for'),
                default => null,
            };
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->query('topic_id'));
        }

        $campaigns = $query->paginate($request->integer('per_page', 25))->withQueryString();

        return $campaigns;
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(StoreCampaignRequest $request)
    {
        Gate::authorize('create:newsletter');

        $campaign = Newsletter::create($request->validated());
        $campaign->refresh();

        return response()->json(
            [
                'message' => 'Campaign created successfully.',
                'campaign' => $campaign->load('topic:id,name,slug'),
            ],
            201,
        );
    }

    /**
     * Display the specified campaign.
     */
    public function show(Newsletter $campaign)
    {
        Gate::authorize('read:newsletter');

        $campaign->load('topic:id,name,slug');
        return $campaign;
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(StoreCampaignRequest $request, Newsletter $campaign)
    {
        Gate::authorize('update:newsletter');

        if ($campaign->sent_at !== null) {
            return response()->json(
                [
                    'message' => 'Cannot modify a campaign that has already been dispatched.',
                ],
                422,
            );
        }

        $campaign->update($request->validated());

        return response()->noContent();
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(Newsletter $campaign)
    {
        Gate::authorize('delete:newsletter');

        if ($campaign->sent_at !== null) {
            return response()->json(
                [
                    'message' => 'Cannot delete a campaign that has already been dispatched. Archive it instead.',
                ],
                422,
            );
        }

        $campaign->delete();

        return response()->noContent();
    }

    /**
     * Dispatch the specified campaign.
     */
    public function dispatch(Newsletter $campaign)
    {
        if ($campaign->sent_at !== null) {
            return response()->json(
                [
                    'message' => 'This campaign has already been dispatched.',
                    'sent_at' => $campaign->sent_at,
                ],
                422,
            );
        }

        NewsletterCampaignJob::dispatch($campaign);
        $campaign->update(['sent_at' => now()]);

        return response()->noContent();
    }

    /**
     * Get analytics of the specified campaign.
     */
    public function analytics(Newsletter $campaign)
    {
        $total_sent = $campaign->logs()->count();
        $total_opened = $campaign->logs()->whereNotNull('opened_at')->count();

        $open_rate = $total_sent > 0 ? round(($total_opened / $total_sent) * 100, 2) : 0;

        $first_opened_at = $campaign->logs()->whereNotNull('opened_at')->min('opened_at');
        $last_opened_at = $campaign->logs()->whereNotNull('opened_at')->max('opened_at');

        return response()->json([
            'campaign' => [
                'id' => $campaign->id,
                'subject' => $campaign->subject,
                'sent_at' => $campaign->sent_at,
                'topic' => $campaign->topic?->only(['id', 'name', 'slug']),
            ],
            'metrics' => [
                'total_dispatched' => $total_sent,
                'total_opened' => $total_opened,
                'unopened' => $total_sent - $total_opened,
                'open_rate' => "{$open_rate}%",
                'first_opened_at' => $first_opened_at,
                'last_opened_at' => $last_opened_at,
            ],
        ]);
    }
}
