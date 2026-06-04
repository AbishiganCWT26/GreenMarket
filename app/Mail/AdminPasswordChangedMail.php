<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AdminPasswordChangedMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $newPassword;
    public $logoPath;

    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Admin Password Has Been Changed - GreenMarket',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-password-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $mail = $this->view('emails.admin-password-changed')
                    ->with([
                        'user' => $this->user,
                        'newPassword' => $this->newPassword,
                        'appName' => config('app.name', 'GreenMarket'),
                    ]);

        return $mail;
    }
}
