<?php

namespace App\Notifications;

use App\Models\ClassroomAssignment;
use Illuminate\Notifications\Messages\MailMessage;

class NewAssignmentNotification extends BaseNotification
{
    public function __construct(public ClassroomAssignment $assignment)
    {
    }

    public function getType(): string
    {
        return 'new_assignment';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Class Assignment')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have been assigned to a new class.")
            ->line("**Subject:** {$this->assignment->subject->name}")
            ->line("**Class:** {$this->assignment->section->classLevel->name} - {$this->assignment->section->name}")
            ->action('View Timetable', route('teacher.timetable.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Class Assignment',
            'message' => "You have been assigned to teach {$this->assignment->subject->name}.",
            'url' => route('teacher.timetable.index'),
            'icon' => 'fas fa-book',
            'color' => 'warning',
        ];
    }
}
