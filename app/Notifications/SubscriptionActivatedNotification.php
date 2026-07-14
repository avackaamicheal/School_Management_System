<?php

namespace App\Notifications;

use App\Models\SchoolSubscription;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionActivatedNotification extends BaseNotification
{
    public function __construct(public SchoolSubscription $subscription)
    {
    }

    public function getType(): string
    {
        return 'subscription_activated';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Activated Successfully!')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your **{$this->subscription->plan->name}** plan has been activated.")
            ->line("Valid until: **{$this->subscription->expires_at->format('M d, Y')}**")
            ->action('Go to Dashboard', route('schooladmin.dashboard', [
                'school' => $notifiable->school->slug
            ]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Subscription Activated',
            'message' => "Your {$this->subscription->plan->name} plan is now active until {$this->subscription->expires_at->format('M d, Y')}.",
            'url' => route('schooladmin.dashboard', ['school' => $notifiable->school->slug]),
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
        ];
    }
}
