<?php

namespace App\Console\Commands;

use App\Jobs\NewsletterCampaignJob;
use App\Models\Newsletter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:campaign-scheduled-tasks')]
#[Description('Process campaigns whose scheduled_for time has arrived')]
class NewsletterCampaignScheduledTasks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Newsletter::whereNull('sent_at')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', now())
            ->chunkById(100, function ($campaigns) {
                foreach ($campaigns as $campaign) {
                    NewsletterCampaignJob::dispatch($campaign);
                    $campaign->update(['sent_at' => now()]);
                }
            });

        $this->info('Campaign tasks dispatched to queue successfully.');
    }
}
