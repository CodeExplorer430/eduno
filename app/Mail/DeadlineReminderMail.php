<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Assignment\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeadlineReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Assignment $assignment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Assignment deadline reminder: '.$this->assignment->title);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.deadline-reminder');
    }
}
