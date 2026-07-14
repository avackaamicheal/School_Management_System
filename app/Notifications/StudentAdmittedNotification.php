<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class StudentAdmittedNotification extends BaseNotification
{
    public function __construct(public User $student)
    {
    }

    public function getType(): string
    {
        return 'student_admitted';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Student Admitted')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new student has been admitted to your school.")
            ->line("**Name:** {$this->student->name}")
            ->line("**Admission No:** {$this->student->studentProfile->admission_number}")
            ->action('View Students', route('student.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Student Admitted',
            'message' => "{$this->student->name} has been admitted.",
            'url' => route('student.index'),
            'icon' => 'fas fa-user-graduate',
            'color' => 'info',
        ];
    }
}
