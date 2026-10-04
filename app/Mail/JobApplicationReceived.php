<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A job application, delivered to the team's inbox with the CV attached.
 * Sent synchronously (no queue worker); the application is stored first.
 */
class JobApplicationReceived extends Mailable
{
    public function __construct(public JobApplication $application) {}

    public function envelope(): Envelope
    {
        $role = $this->application->vacancy?->title ?? 'General application';

        return new Envelope(
            subject: "Job application: {$role} ({$this->application->name})",
            replyTo: filled($this->application->email)
                ? [new Address($this->application->email, $this->application->name)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.job-application-received');
    }

    /**
     * @return list<Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->application->cvContents(), $this->application->cv_original_name)
                ->withMime($this->application->cv_mime),
        ];
    }
}
