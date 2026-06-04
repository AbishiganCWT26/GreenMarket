<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotificationMail extends Mailable
{
    use SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';

        if ($this->mailData['type'] === 'farmer') {
            $subject = 'New Order Received - Prepare for Pickup';
        } elseif ($this->mailData['type'] === 'lead_farmer') {
            $subject = 'New COD Order Received - Action Required';
        }

        return $this->subject($subject)
                    ->view('emails.order-notification')
                    ->with([
                        'mailData' => $this->mailData,
                    ]);
    }
}
