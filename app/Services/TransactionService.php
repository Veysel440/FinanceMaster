<?php

namespace App\Services;

use App\Interface\TransactionRepositoryInterface;
use App\Services\BudgetService;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected BudgetService $budgetService
    ) {}

    public function getUserTransactions(array $filters = [])
    {
        return $this->transactionRepository->getByUserId(Auth::id(), $filters);
    }

    public function createTransaction(array $data)
    {
        $data['user_id'] = Auth::id();
        $transaction = $this->transactionRepository->create($data);

        if ($transaction->type === 'expense') {
            $this->budgetService->checkBudgetsForTransaction($transaction);
        }

        return $transaction;
    }

    public function getTransaction(int $id)
    {
        $transaction = $this->transactionRepository->findById($id);
        return ($transaction && $transaction->user_id === Auth::id()) ? $transaction : null;
    }

    public function updateTransaction(int $id, array $data): bool
    {
        $transaction = $this->getTransaction($id);
        if (!$transaction) return false;

        $updated = $this->transactionRepository->update($id, $data);

        if ($updated && $data['type'] === 'expense') {
            $updatedTransaction = $this->getTransaction($id);
            $this->budgetService->checkBudgetsForTransaction($updatedTransaction);
        }

        return $updated;
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = $this->getTransaction($id);
        return $transaction ? $this->transactionRepository->delete($id) : false;
    }
}
