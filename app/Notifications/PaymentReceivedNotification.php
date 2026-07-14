<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceivedNotification extends BaseNotification
{
    public function __construct(public Payment $payment)
    {
    }

    public function getType(): string
    {
        return 'payment_received';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Received')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A payment has been received.")
            ->line("**Student:** {$this->payment->invoice->student->name}")
            ->line("**Amount Paid:** ₦" . number_format($this->payment->amount))
            ->line("**Reference:** {$this->payment->reference}");
    }

    public function toArray(object $notifiable): array
    {
        $url = match (true) {
            $notifiable->hasRole('Parent') => route('parent.dashboard', ['school' => $notifiable->school->slug]),
            $notifiable->hasRole('SchoolAdmin') => route('invoices.index', ['school' => $notifiable->school->slug]),
            default => '#',
        };

        return [
            'title' => 'Payment Received',
            'message' => "₦" . number_format($this->payment->amount) . " payment confirmed.",
            'url' => $url,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
        ];
    }
}
