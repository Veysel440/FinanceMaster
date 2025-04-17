<?php

namespace App\Services;

use App\Repositories\BudgetRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BudgetService
{
    protected $budgetRepository;
    protected $categoryRepository;
    protected $notificationService;

    public function __construct(
        BudgetRepositoryInterface $budgetRepository,
        CategoryRepositoryInterface $categoryRepository,
        NotificationService $notificationService
    ) {
        $this->budgetRepository = $budgetRepository;
        $this->categoryRepository = $categoryRepository;
        $this->notificationService = $notificationService;
    }

    public function getUserBudgets(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->budgetRepository->getByUserId(Auth::id());
    }

    public function createBudget(array $data): \App\Models\Budget
    {
        $data['user_id'] = Auth::id();
        return $this->budgetRepository->create($data);
    }

    public function getBudget(int $id): ?\App\Models\Budget
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

        return [
            'status' => $remaining >= 0 ? 'ok' : 'exceeded',
            'spent' => $spent,
            'remaining' => $remaining,
            'message' => $remaining >= 0 ? 'Bütçe limitinizde.' : 'Bütçe limitiniz aşıldı!',
        ];
    }

    public function checkBudgetsForTransaction($transaction): void
    {
        $budgets = $this->budgetRepository->getByUserId($transaction->user_id)
            ->where('category_id', $transaction->category_id)
            ->where('month', Carbon::parse($transaction->date)->format('Y-m-01'));

        foreach ($budgets as $budget) {
            $status = $this->checkBudgetStatus($budget->id);
            if ($status['status'] === 'exceeded') {
                $this->notificationService->sendBudgetLimitExceededNotification(
                    $budget,
                    $status['spent'],
                    $status['remaining']
                );
            }
        }
    }
}
