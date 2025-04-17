<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\BudgetLimitExceededNotification;
use App\Notifications\MonthlySummaryNotification;

class NotificationService
{
    public function sendBudgetLimitExceededNotification($budget, $spent, $remaining): void
    {
        $user = User::find($budget->user_id);
        if ($user) {
            $user->notify(new BudgetLimitExceededNotification($budget, $spent, $remaining));
        }
    }

    public function sendMonthlySummaryNotification($user, array $summary): void
    {
        $user->notify(new MonthlySummaryNotification($summary));
    }
}
