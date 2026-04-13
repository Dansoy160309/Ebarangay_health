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
        return (new MailMessage)
            ->subject('New Announcement: ' . $this->announcement->title)
            ->view('emails.announcements.broadcast', [
                'announcement' => $this->announcement,
                'recipientName' => $notifiable->full_name ?? 'Patient',
                'appUrl' => config('app.url'),
            ]);
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

