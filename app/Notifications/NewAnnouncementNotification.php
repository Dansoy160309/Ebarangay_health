<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
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
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'New health advisory: ' . $this->announcement->title,
            'announcement_id' => $this->announcement->id,
            'status' => 'sent',
            'channel' => 'in_app',
            'category' => 'announcement',
        ];
    }
}

