<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class TeacherAddedNotification extends BaseNotification
{
    public function __construct(public User $teacher) {}

    public function getType(): string
    {
        return 'teacher_added';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Teacher Added')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new teacher has been added to your school.")
            ->line("**Name:** {$this->teacher->name}")
            ->line("**Email:** {$this->teacher->email}")
            ->action('View Teachers', route('teachers.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'New Teacher Added',
            'message' => "{$this->teacher->name} has been added as a teacher.",
            'url'     => route('teachers.index'),
            'icon'    => 'fas fa-chalkboard-teacher',
            'color'   => 'success',
        ];
    }
}
