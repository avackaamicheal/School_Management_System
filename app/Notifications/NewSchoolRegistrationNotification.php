<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Notifications\Messages\MailMessage;

class NewSchoolRegistrationNotification extends BaseNotification
{
    public function __construct(public School $school)
    {
    }

    public function getType(): string
    {
        return 'new_school_registration';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New School Registration — Action Required')
            ->greeting('Hello Super Admin,')
            ->line("A new school has registered and paid their subscription.")
            ->line("**School:** {$this->school->name}")
            ->line("**Email:** {$this->school->email}")
            ->line("**Address:** {$this->school->address}")
            ->action('View Schools', route('school.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New School Registration',
            'message' => "{$this->school->name} has registered and is now active.",
            'url' => route('school.index'),
            'icon' => 'fas fa-school',
            'color' => 'primary',
        ];
    }
}
