<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentBookedNotification extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Determine the channels for notification.
     */
    public function via($notifiable)
    {
        // Send both email and database notification
        return ['mail', 'database'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appointment Booked Successfully')
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line('Your appointment for **' . $this->appointment->service . '** has been successfully booked.')
            ->line('📅 Date & Time: ' . $this->appointment->scheduled_at->format('F d, Y h:i A'))
            ->line('Status: ' . ucfirst($this->appointment->status))
            ->line('Thank you for using E-Barangay Health!');
    }

    /**
     * Store the notification in the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'service' => $this->appointment->service,
            'scheduled_at' => $this->appointment->scheduled_at->format('F d, Y h:i A'),
            'status' => $this->appointment->status,
            'message' => 'Your appointment for ' . $this->appointment->service . ' on ' . $this->appointment->scheduled_at->format('F d, Y h:i A') . ' has been booked.',
        ];
    }

    /**
     * Optional: Array representation for other channels if needed.
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
