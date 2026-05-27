<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewShipmentNotification extends Notification
{
    use Queueable;

    public $shipmentData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($shipmentData)
    {
        $this->shipmentData = $shipmentData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'sms'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        // Fallback route if the exact route name is different
        $link = '#';
        if (\Route::has('delivery-rider.incoming-shipments')) {
            $link = route('delivery-rider.incoming-shipments');
        }

        return [
            'order_number' => $this->shipmentData['order_number'] ?? '',
            'district' => $this->shipmentData['district'] ?? '',
            'eta' => $this->shipmentData['eta'] ?? '',
            'link' => $link,
            'message' => 'New delivery available in your district.'
        ];
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        $orderNumber = $this->shipmentData['order_number'] ?? 'N/A';
        $eta = $this->shipmentData['eta'] ?? 'N/A';
        $cutoffTime = $this->shipmentData['cutoff_time'] ?? 'soon';
        
        return "New delivery available in your district! Order #{$orderNumber}. Bus arrives at {$eta}. Accept before {$cutoffTime}. Login to claim.";
    }
}
