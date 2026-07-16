<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $name;

    public function __construct(string $otpCode, string $name)
    {
        $this->otpCode = $otpCode;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Reset Password - Lacak.app',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.reset-password',
            with: [
                'otpCode' => $this->otpCode,
                'name' => $this->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
