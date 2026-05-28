<?php

namespace App\Notifications;

use App\Models\SchoolSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(public SchoolSubscription $subscription)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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
            'message' => "Your {$this->subscription->plan->name} plan is now active.",
            'expires_at' => $this->subscription->expires_at,
        ];
    }
}
