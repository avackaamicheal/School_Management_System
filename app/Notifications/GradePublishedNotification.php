<?php

namespace App\Notifications;

use App\Models\GradeRecord;
use Illuminate\Notifications\Messages\MailMessage;

class GradePublishedNotification extends BaseNotification
{
    public function __construct(public GradeRecord $grade)
    {
    }

    public function getType(): string
    {
        return 'grade_published';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Grade Published')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new grade has been published.")
            ->line("**Student:** {$this->grade->student->name}")
            ->line("**Subject:** {$this->grade->subject->name}")
            ->line("**Score:** {$this->grade->total_score}%");
    }

    public function toArray(object $notifiable): array
    {
        $url = match (true) {
            $notifiable->hasRole('Parent') => route('parent.dashboard', ['school' => $notifiable->school->slug]),
            $notifiable->hasRole('Student') => route('student.dashboard', ['school' => $notifiable->school->slug]),
            default => '#',
        };

        return [
            'title' => 'Grade Published',
            'message' => "{$this->grade->subject->name} grade published: {$this->grade->total_score}%",
            'url' => $url,
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary',
        ];
    }
}
