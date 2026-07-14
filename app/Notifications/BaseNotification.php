<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    abstract public function getType(): string;
    abstract public function toArray(object $notifiable): array;

    public function via(object $notifiable): array
    {
        $channels = [];
        $type     = $this->getType();
        $isCritical = in_array($type, \App\Models\NotificationPreference::CRITICAL);

        if ($notifiable->wantsNotification($type, 'in_app')) {
            $channels[] = 'database';
        }

        if ($isCritical && $notifiable->wantsNotification($type, 'email')) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
