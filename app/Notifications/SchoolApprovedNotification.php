<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Notifications\Messages\MailMessage;

class SchoolApprovedNotification extends BaseNotification
{
    public function __construct(public School $school)
    {
    }

    public function getType(): string
    {
        return 'school_approved';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your School Registration Has Been Approved!')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! Your school **{$this->school->name}** has been approved.")
            ->line('You can now log in and begin setting up your school.')
            ->action('Login Now', route('login'))
            ->line('Welcome to Axia SMS!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'School Registration Approved',
            'message' => "Your school {$this->school->name} has been approved. Welcome aboard!",
            'url' => route('login'),
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
        ];
    }
}
