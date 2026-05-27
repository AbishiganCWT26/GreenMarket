<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ShipmentUnassignedAlert extends Notification
{
    use Queueable;

    public $alertData;
    public $isEscalation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($alertData, $isEscalation = false)
    {
        $this->alertData = $alertData;
        $this->isEscalation = $isEscalation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Returns ['database'] (first alert) or ['database', 'sms'] (escalation) depending on urgency
        return $this->isEscalation ? ['database', 'sms'] : ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $link = '#';
        // Check if admin shipment assignment route exists, else fallback
        if (\Route::has('admin.shipments.assign')) {
            $link = route('admin.shipments.assign', ['id' => $this->alertData['shipment_id'] ?? 0]);
        } elseif (\Route::has('admin.shipments.index')) {
            $link = route('admin.shipments.index');
        }

        return [
            'order_number' => $this->alertData['order_number'] ?? '',
            'bus_eta' => $this->alertData['bus_eta'] ?? '',
            'minutes_remaining' => $this->alertData['minutes_remaining'] ?? '',
            'link' => $link,
            'message' => "Order #{$this->alertData['order_number']} is unassigned. Bus arriving in {$this->alertData['minutes_remaining']} minutes."
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
        $orderNumber = $this->alertData['order_number'] ?? 'N/A';
        $minutesRemaining = $this->alertData['minutes_remaining'] ?? 'N/A';
        
        return "URGENT: Order #{$orderNumber} unassigned! Bus arriving in {$minutesRemaining} minutes. Assign rider now.";
    }
}
