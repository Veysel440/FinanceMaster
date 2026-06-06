<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Single entry-point for every event with financial meaning: transactions,
 * budget overruns, goal progress. Writes JSON to the 'financial' channel
 * (90-day retention) and stamps every record with the current request_id
 * so it can be cross-referenced with api.log and auth.log entries.
 */
class FinancialLogger
{
    private function context(array $extra): array
    {
        return array_merge([
            'request_id' => app()->bound('request_id') ? app('request_id') : null,
            'timestamp'  => now()->toIso8601String(),
        ], $extra);
    }

    public function transactionCreated(
        int $userId,
        int $transactionId,
        string $type,
        float $amount,
        int $categoryId
    ): void {
        Log::channel('financial')->info('TRANSACTION_CREATED', $this->context([
            'user_id'        => $userId,
            'transaction_id' => $transactionId,
            'type'           => $type,
            'amount'         => $amount,
            'category_id'    => $categoryId,
        ]));
    }

    public function transactionUpdated(int $userId, int $transactionId, array $changes): void
    {
        Log::channel('financial')->info('TRANSACTION_UPDATED', $this->context([
            'user_id'        => $userId,
            'transaction_id' => $transactionId,
            'changes'        => $changes,
        ]));
    }

    public function transactionDeleted(int $userId, int $transactionId): void
    {
        Log::channel('financial')->warning('TRANSACTION_DELETED', $this->context([
            'user_id'        => $userId,
            'transaction_id' => $transactionId,
        ]));
    }

    public function budgetExceeded(
        int $userId,
        int $budgetId,
        float $limit,
        float $spent,
        float $percentage
    ): void {
        Log::channel('financial')->warning('BUDGET_EXCEEDED', $this->context([
            'user_id'    => $userId,
            'budget_id'  => $budgetId,
            'limit'      => $limit,
            'spent'      => $spent,
            'percentage' => $percentage,
            'overage'    => round($spent - $limit, 2),
        ]));
    }

    public function goalAchieved(int $userId, int $goalId, float $targetAmount): void
    {
        Log::channel('financial')->info('GOAL_ACHIEVED', $this->context([
            'user_id'       => $userId,
            'goal_id'       => $goalId,
            'target_amount' => $targetAmount,
        ]));
    }

    public function goalProgressUpdated(
        int $userId,
        int $goalId,
        float $currentAmount,
        float $targetAmount
    ): void {
        Log::channel('financial')->info('GOAL_PROGRESS', $this->context([
            'user_id'        => $userId,
            'goal_id'        => $goalId,
            'current_amount' => $currentAmount,
            'target_amount'  => $targetAmount,
            'percentage'     => $targetAmount > 0
                ? round(($currentAmount / $targetAmount) * 100, 1)
                : 0,
        ]));
    }
}
