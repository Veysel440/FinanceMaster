<?php

namespace App\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;
use App\Services\ReportService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $reportService = app(ReportService::class);
            $notificationService = app(NotificationService::class);

            User::all()->each(function ($user) use ($reportService, $notificationService) {
                $summary = $reportService->getSummary('monthly', now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString());
                $notificationService->sendMonthlySummaryNotification($user, $summary);
            });
        })->monthlyOn(1, '00:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
