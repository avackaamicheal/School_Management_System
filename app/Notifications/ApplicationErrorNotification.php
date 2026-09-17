<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Throwable;

class ApplicationErrorNotification extends Notification
{
    use Queueable;

    public function __construct(protected Throwable $exception) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Application Error: ' . get_class($this->exception))
            ->line('An unhandled exception occurred.')
            ->line('**Message:** ' . $this->exception->getMessage())
            ->line('**File:** ' . $this->exception->getFile() . ':' . $this->exception->getLine())
            ->line('**Environment:** ' . app()->environment());
    }
}
