<?php

namespace App\Notifications;

use App\Models\School;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiringNotification extends BaseNotification
{
    public function __construct(public School $school) {}

    public function getType(): string
    {
        return 'subscription_expiring';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Subscription Expires in 7 Days')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your subscription for **{$this->school->name}** expires on **{$this->school->subscription_expires_at->format('M d, Y')}**.")
            ->line('Renew now to avoid any interruption to your service.')
            ->action('Renew Subscription', route('subscription.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Subscription Expiring Soon',
            'message' => "Your subscription expires on {$this->school->subscription_expires_at->format('M d, Y')}. Renew now.",
            'url'     => route('subscription.index'),
            'icon'    => 'fas fa-exclamation-triangle',
            'color'   => 'warning',
        ];
    }
}
