<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $userFirstName;
    public string $otpType;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $userFirstName = 'User', string $otpType = 'Email Verification')
    {
        $this->otp = $otp;
        $this->userFirstName = $userFirstName;
        $this->otpType = $otpType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->otpType} Code - SPORTYNIX HUB",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-otp',
            with: [
                'otp' => $this->otp,
                'userFirstName' => $this->userFirstName,
                'otpType' => $this->otpType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
