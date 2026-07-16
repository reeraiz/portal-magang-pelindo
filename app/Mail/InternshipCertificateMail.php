<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class InternshipCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $intern;
    public $certificatePath;
    public $fileName;

    public function __construct(User $intern, $certificatePath, $fileName)
    {
        $this->intern = $intern;
        $this->certificatePath = $certificatePath;
        $this->fileName = $fileName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('sarhamsan32@gmail.com', 'Pelindo'),
            subject: 'Sertifikat Magang - Pelindo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->certificatePath)
                ->as($this->fileName)
                ->withMime('application/pdf'),
        ];
    }
}
