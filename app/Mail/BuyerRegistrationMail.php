<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerRegistrationMail extends Mailable
{
    use SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('Welcome to GreenMarket - Your Account Details')
                    ->view('emails.buyerRegistrationMail')
                    ->with($this->emailData);
    }
}
