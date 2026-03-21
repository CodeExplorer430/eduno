<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Announcement\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementPublishedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Announcement $announcement)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->announcement->title);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.announcement-published');
    }
}
