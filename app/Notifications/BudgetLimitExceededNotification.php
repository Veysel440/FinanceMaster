<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BudgetLimitExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $budget;
    protected $spent;
    protected $remaining;

    public function __construct($budget, $spent, $remaining)
    {
        $this->budget = $budget;
        $this->spent = $spent;
        $this->remaining = $remaining;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bütçe Limit Aşımı Uyarısı')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line("**{$this->budget->category->name}** kategorisi için belirlediğiniz bütçe limiti aşıldı.")
            ->line('**Bütçe Detayları:**')
            ->line('- Bütçe Miktarı: ' . number_format($this->budget->amount, 2) . ' ' . $notifiable->currency)
            ->line('- Harcanan Miktar: ' . number_format($this->spent, 2) . ' ' . $notifiable->currency)
            ->line('- Aşım Miktarı: ' . number_format(abs($this->remaining), 2) . ' ' . $notifiable->currency)
            ->action('Bütçenizi Kontrol Edin', url('/budgets'))
            ->line('Harcamalarınızı gözden geçirmek için bütçe sayfasını ziyaret edebilirsiniz.');
    }
}
