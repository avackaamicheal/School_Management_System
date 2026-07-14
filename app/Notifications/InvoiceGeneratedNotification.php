<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceGeneratedNotification extends BaseNotification
{
    public function __construct(public Invoice $invoice)
    {
    }

    public function getType(): string
    {
        return 'invoice_generated';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Invoice Generated')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new invoice has been generated for your child.")
            ->line("**Student:** {$this->invoice->student->name}")
            ->line("**Invoice No:** {$this->invoice->invoice_number}")
            ->line("**Amount:** ₦" . number_format($this->invoice->total_amount))
            ->line("**Due Date:** {$this->invoice->due_date}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Invoice',
            'message' => "Invoice {$this->invoice->invoice_number} — ₦" . number_format($this->invoice->total_amount),
            'url' => route('parent.dashboard', ['school' => $notifiable->school->slug]),
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'warning',
        ];
    }
}
