<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Aylık Finansal Özet')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Geçen ayın finansal özetiniz aşağıda yer alıyor:')
            ->line('**Özet Detayları:**')
            ->line('- Toplam Gelir: ' . number_format($this->summary['income'], 2) . ' ' . $notifiable->currency)
            ->line('- Toplam Gider: ' . number_format($this->summary['expense'], 2) . ' ' . $notifiable->currency)
            ->line('- Bakiye: ' . number_format($this->summary['balance'], 2) . ' ' . $notifiable->currency)
            ->action('Raporları Görüntüle', url('/reports'))
            ->line('Daha fazla detay için raporlar sayfasını ziyaret edebilirsiniz.');
    }
}
