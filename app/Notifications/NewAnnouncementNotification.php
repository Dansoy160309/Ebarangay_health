<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Announcement;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected Announcement $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = 'New Announcement: ' . $this->announcement->title;

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->full_name ?? 'Patient') . ',')
            ->line('A new announcement has been posted for all patients.')
            ->line('Title: ' . $this->announcement->title)
            ->line('Message:')
            ->line($this->announcement->message);

        if ($this->announcement->expires_at) {
            $mailMessage->line('Expires: ' . $this->announcement->expires_at->format('F d, Y'));
        }

        return $mailMessage
            ->line('Please log in to your account to view any related details or updates.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'New announcement: ' . $this->announcement->title,
            'announcement_id' => $this->announcement->id,
            'status' => 'sent',
            'channel' => 'in_app_email',
            'category' => 'announcement',
        ];
    }
}

