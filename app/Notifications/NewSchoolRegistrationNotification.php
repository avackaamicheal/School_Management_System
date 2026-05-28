<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSchoolRegistrationNotification extends Notification
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
            ->subject('New School Registration Pending Approval')
            ->greeting('Hello Super Admin,')
            ->line("A new school has registered and is awaiting your approval.")
            ->line("**School:** {$this->school->name}")
            ->line("**Email:** {$this->school->email}")
            ->line("**Address:** {$this->school->address}")
            ->action('Review Registration', route('school.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'New School Registration',
            'message'   => "{$this->school->name} has registered and is awaiting approval.",
            'school_id' => $this->school->id,
        ];
    }
}
