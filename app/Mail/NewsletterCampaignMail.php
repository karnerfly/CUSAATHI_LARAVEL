<?php

namespace App\Mail;

use App\Models\Newsletter\Log;
use App\Models\Newsletter\Newsletter;
use App\Models\Newsletter\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Newsletter $campaign, public Subscriber $subscriber, public Log $log)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->campaign->subject);
    }

    public function headers(): Headers
    {
        $unsubscribe_url = route('api.newsletter.unsubscribe', [
            'subscriber' => $this->subscriber->unsubscribe_token,
        ]);

        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$unsubscribe_url}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(htmlString: $this->buildEmailHtml());
    }

    private function buildEmailHtml(): string
    {
        $client_url = config('app.client_url');
        $tracking_url = route('api.newsletter.track-open', ['log_id' => $this->log->id]);
        $unsubscribe_url =
            rtrim($client_url, '/') . '/newsletter/unsubscribe?token=' . $this->subscriber->unsubscribe_token;

        return <<<HTML
            {$this->campaign->content}
            <br><hr>
            <p style="font-size: 12px; color: #888;">
                You received this email because you are subscribed to our newsletter.
                <a href="{$unsubscribe_url}">Unsubscribe</a>
            </p>
            <img src="{$tracking_url}" width="1" height="1" style="display:none;" alt="" />
        HTML;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
