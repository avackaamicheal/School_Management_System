<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SchoolRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public School $school,
        public string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your School Registration Was Not Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line("Unfortunately, your school registration for **{$this->school->name}** was not approved.")
            ->line("**Reason:** {$this->reason}")
            ->line('If you believe this is a mistake, please contact our support team.')
            ->action('Contact Support', url('/'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'School Registration Rejected',
            'message' => "Your school {$this->school->name} was not approved. Reason: {$this->reason}",
        ];
    }
}
