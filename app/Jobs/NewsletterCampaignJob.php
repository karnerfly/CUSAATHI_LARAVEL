<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\Newsletter\Log;
use App\Models\Newsletter\Newsletter;
use App\Models\Newsletter\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(public Newsletter $campaign)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscriber_query = Subscriber::active();

        if ($this->campaign->topic_id) {
            $subscriber_query->whereHas('topics', function ($query) {
                $query->whereKey($this->campaign->topic_id);
            });
        }

        $subscriber_query->chunkById(250, function ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $log = Log::firstOrCreate(
                    [
                        'newsletter_id' => $this->campaign->id,
                        'subscriber_id' => $subscriber->id,
                    ],
                    [
                        'sent_at' => now(),
                    ],
                );

                if ($log->wasRecentlyCreated) {
                    Mail::to($subscriber->email)->queue(new NewsletterCampaignMail($this->campaign, $subscriber, $log));
                }
            }
        });
    }
}
