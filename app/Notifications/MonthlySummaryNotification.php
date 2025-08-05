<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = app()->getLocale();
        $currency = $notifiable->currency ?? 'TRY';

        $format = $locale === 'tr'
            ? fn($val) => number_format($val, 2, ',', '.')
            : fn($val) => number_format($val, 2);

        return (new MailMessage)
            ->subject('Aylık Finansal Özet')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Geçen ayın finansal özetiniz aşağıda yer alıyor:')
            ->line('**Özet Detayları:**')
            ->line('- Toplam Gelir: ' . $format($this->summary['income']) . ' ' . $currency)
            ->line('- Toplam Gider: ' . $format($this->summary['expense']) . ' ' . $currency)
            ->line('- Bakiye: ' . $format($this->summary['balance']) . ' ' . $currency)
            ->action('Raporları Görüntüle', url('/reports'))
            ->line('Daha fazla detay için raporlar sayfasını ziyaret edebilirsiniz.');
    }
}
