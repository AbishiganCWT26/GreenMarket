<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RiderDispatchNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $shipmentData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($shipmentData)
    {
        $this->shipmentData = $shipmentData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $districtName = $this->shipmentData['district'] ?? 'Your District';
        return $this->subject("New Delivery Available in {$districtName}")
                    ->view('emails.rider-dispatch-notification');
    }
}
