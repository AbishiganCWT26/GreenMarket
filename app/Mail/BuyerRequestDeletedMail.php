<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerRequestDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        $mail = $this->subject('Buyer Request Deleted')
                    ->view('emails.buyer-request-deleted')
                    ->with([
                        'mailData' => $this->mailData
                    ]);

        return $mail;
    }
}
