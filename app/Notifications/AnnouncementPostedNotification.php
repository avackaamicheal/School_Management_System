<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Notifications\Messages\MailMessage;

class AnnouncementPostedNotification extends BaseNotification
{
    public function __construct(public Announcement $announcement)
    {
    }

    public function getType(): string
    {
        return 'announcement_posted';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Announcement: ' . $this->announcement->title)
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new announcement has been posted.")
            ->line("**{$this->announcement->title}**")
            ->line($this->announcement->content);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'author' => $this->announcement->author->name,
            'announcement_id' => $this->announcement->id,
            'message' => 'New announcement posted.',
            'icon' => 'fas fa-bullhorn',
            'color' => 'info',
            'url' => resolveRoute('announcements.index', [], $notifiable),
        ];
    }
}
