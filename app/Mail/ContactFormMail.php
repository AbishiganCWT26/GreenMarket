<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable  // REMOVE "implements ShouldQueue"
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Contact Form Message: ' . ($this->data['subject'] ?? 'No Subject'))
                    ->view('emails.contact-form')
                    ->with('data', $this->data);
    }
}
