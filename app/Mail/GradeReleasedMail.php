<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Grade\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradeReleasedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Grade $grade)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your grade has been released');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.grade-released');
    }
}
