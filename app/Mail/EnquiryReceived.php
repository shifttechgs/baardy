<?php

namespace App\Mail;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A "Get in touch" enquiry, delivered to the team's inbox with a link to the
 * lead in the admin panel, where it is worked.
 *
 * Sent synchronously after the lead is stored (see EnquiryController): the
 * site has no queue worker, and the lead is already safe if this fails.
 */
class EnquiryReceived extends Mailable
{
    /**
     * @param  array{name: string, phone: string, email?: ?string, interest: string, branch: string, message?: ?string}  $enquiry  this submission, as sent
     */
    public function __construct(public Lead $lead, public array $enquiry) {}

    public function isRepeat(): bool
    {
        return ! $this->lead->wasRecentlyCreated;
    }

    public function envelope(): Envelope
    {
        $prefix = $this->isRepeat() ? 'Repeat enquiry' : 'New lead';

        return new Envelope(
            subject: "{$prefix} {$this->lead->reference}: {$this->enquiry['interest']} ({$this->enquiry['branch']})",
            replyTo: filled($this->enquiry['email'] ?? null)
                ? [new Address($this->enquiry['email'], $this->enquiry['name'])]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.enquiry-received',
            with: [
                'isRepeat' => $this->isRepeat(),
                'promotion' => $this->lead->promotion,
                'leadUrl' => LeadResource::getUrl('view', ['record' => $this->lead], panel: 'admin'),
            ],
        );
    }
}
