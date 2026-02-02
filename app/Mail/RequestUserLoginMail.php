<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestUserLoginMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id_user;
    /**
     * Create a new message instance.
     */
    public function __construct(public string $email)
    {
        $id_user = User::where('email', $email)->first();
        if ($id_user) {
            $this->id_user = $id_user->id;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request User Login Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
       return new Content(
            markdown: 'emails.request-login', // Kita gunakan markdown agar rapi
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
