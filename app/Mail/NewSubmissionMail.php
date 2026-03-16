<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Submission\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New submission received');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.new-submission');
    }
}
