<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoleChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $oldRole,
        public string $newRole
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Role Has Changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.role-changed',
            with: [
                'user' => $this->user,
                'oldRole' => $this->oldRole,
                'newRole' => $this->newRole,
                'appName' => config('app.name'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
