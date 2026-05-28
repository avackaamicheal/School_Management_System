<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SchoolApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public School $school) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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
            'title'     => 'School Registration Approved',
            'message'   => "Your school {$this->school->name} has been approved.",
            'school_id' => $this->school->id,
        ];
    }
}
