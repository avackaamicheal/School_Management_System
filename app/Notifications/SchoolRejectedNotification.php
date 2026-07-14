<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Notifications\Messages\MailMessage;

class SchoolRejectedNotification extends BaseNotification
{
    public function __construct(
        public School $school,
        public string $reason
    ) {}

    public function getType(): string
    {
        return 'school_rejected';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your School Has Been Deactivated')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your school **{$this->school->name}** has been deactivated.")
            ->line("**Reason:** {$this->reason}")
            ->line('Please contact our support team if you believe this is a mistake.')
            ->action('Contact Support', url('/'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'School Deactivated',
            'message' => "{$this->school->name} has been deactivated. Reason: {$this->reason}",
            'url'     => route('schooladmin.rejected'),
            'icon'    => 'fas fa-ban',
            'color'   => 'danger',
        ];
    }
}
