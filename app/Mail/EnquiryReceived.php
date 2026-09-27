<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A "Get in touch" enquiry, delivered to the client's inbox.
 *
 * Sent synchronously: the site has no queue worker, and the visitor's
 * success message should mean the enquiry was actually handed to the mailer.
 */
class EnquiryReceived extends Mailable
{
    /**
     * @param  array{name: string, phone: string, email?: ?string, interest: string, branch: string, message?: ?string}  $enquiry
     */
    public function __construct(public array $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Website enquiry: {$this->enquiry['interest']} ({$this->enquiry['branch']})",
            replyTo: filled($this->enquiry['email'] ?? null)
                ? [new Address($this->enquiry['email'], $this->enquiry['name'])]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.enquiry-received',
        );
    }
}
