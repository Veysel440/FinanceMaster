<?php

namespace App\Services;

use App\Interface\BudgetRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Models\Transaction;
use App\Notifications\BudgetLimitExceededNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BudgetService
{
    public function __construct(
        protected BudgetRepositoryInterface $budgetRepository,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getUserBudgets()
    {
        return $this->budgetRepository->getByUserId(Auth::id());
    }

    public function createBudget(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->budgetRepository->create($data);
    }

    public function getBudget(int $id)
    {
        $budget = $this->budgetRepository->findById($id);
        return $budget && $budget->user_id === Auth::id() ? $budget : null;
    }

    public function updateBudget(int $id, array $data): bool
    {
        $budget = $this->getBudget($id);
        return $budget ? $this->budgetRepository->update($id, $data) : false;
    }

    public function deleteBudget(int $id): bool
    {
        $budget = $this->getBudget($id);
        return $budget ? $this->budgetRepository->delete($id) : false;
    }

    public function checkBudgetStatus(int $budgetId): array
    {
        $budget = $this->getBudget($budgetId);
        if (!$budget) {
            return ['status' => 'error', 'message' => 'Bütçe bulunamadı.'];
        }

        $spent = $this->budgetRepository->getSpentAmount($budget->category_id, $budget->month);
        $remaining = $budget->amount - $spent;

        if ($remaining < 0) {
            Auth::user()->notify(new BudgetLimitExceededNotification($budget, $spent, $remaining));
        }

        return [
            'status'    => $remaining >= 0 ? 'ok' : 'exceeded',
            'spent'     => $spent,
            'remaining' => $remaining,
            'message'   => $remaining >= 0 ? 'Bütçe limitinizde.' : 'Bütçe limitiniz aşıldı!',
        ];
    }

    /**
     * After a new transaction is recorded, iterate the user's active budgets
     * and fire over-budget notifications where the spending limit is exceeded.
     * No-op for income-type transactions.
     */
    public function checkBudgetsForTransaction(int $userId, Transaction $transaction): void
    {
        if ($transaction->type !== 'expense') {
            return;
        }

        $budgets = $this->budgetRepository->getByUserId($userId);

        foreach ($budgets as $budget) {
            $this->checkBudgetStatus($budget->id);
        }
    }
}
