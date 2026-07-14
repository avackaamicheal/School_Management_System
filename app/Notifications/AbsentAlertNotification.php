<?php

namespace App\Notifications;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class AbsentAlertNotification extends BaseNotification
{
    public function __construct(public Attendance $attendance)
    {
    }

    public function getType(): string
    {
        return 'absent_alert';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $student = $this->attendance->student ?? User::find($this->attendance->student_id);
        $studentName = $student->name ?? 'your child';

        return (new MailMessage)
            ->subject('Attendance Alert — ' . $studentName)
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your child was marked absent today.")
            ->line("**Student:** {$studentName}")
            ->line("**Date:** {$this->attendance->date}")
            ->line("**Remarks:** " . ($this->attendance->remarks ?? 'None'));
    }

    public function toArray(object $notifiable): array
    {
        $student = $this->attendance->student ?? User::find($this->attendance->student_id);
        $studentName = $student->name ?? 'Student';

        return [
            'title' => 'Absent Alert',
            'message' => "{$studentName} was marked absent on {$this->attendance->date}.",
            'url' => route('parent.dashboard', ['school' => $notifiable->school->slug]),
            'icon' => 'fas fa-user-times',
            'color' => 'danger',
        ];
    }
}
