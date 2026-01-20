<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $user;

    public function __construct($subject, $message, $user)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.user_update')
            ->with([
                'user' => $this->user,
                'content' => $this->message
            ]);
    }
}
