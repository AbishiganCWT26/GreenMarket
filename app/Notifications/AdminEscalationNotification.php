<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminEscalationNotification extends Notification
{
    use Queueable;

    public $escalationData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($escalationData)
    {
        $this->escalationData = $escalationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Returns ['database', 'sms'] for recurring 10-minute alerts
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
        return [
            'escalation_level' => $this->escalationData['escalation_level'] ?? 1,
            'minutes_remaining' => $this->escalationData['minutes_remaining'] ?? '',
            'order_details' => $this->escalationData['order_details'] ?? '',
            'order_number' => $this->escalationData['order_number'] ?? '',
            'message' => "ESCALATION: Order #{$this->escalationData['order_number']} still unassigned. Immediate action required."
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
        $orderNumber = $this->escalationData['order_number'] ?? 'N/A';
        $minutesRemaining = $this->escalationData['minutes_remaining'] ?? 'N/A';
        
        return "ESCALATION: Order #{$orderNumber} still unassigned! {$minutesRemaining} min until bus arrival. Immediate action required.";
    }
}
