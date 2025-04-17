<?php

namespace App\Services;

use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $transactionRepository;
    protected $budgetService;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        BudgetService $budgetService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->budgetService = $budgetService;
    }

    public function getUserTransactions(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->transactionRepository->getByUserId(Auth::id(), $filters);
    }

    public function createTransaction(array $data): \App\Models\Transaction
    {
        $data['user_id'] = Auth::id();
        $transaction = $this->transactionRepository->create($data);

        if ($transaction->type === 'expense') {
            $this->budgetService->checkBudgetsForTransaction($transaction);
        }

        return $transaction;
    }

    public function getTransaction(int $id): ?\App\Models\Transaction
    {
        $transaction = $this->transactionRepository->findById($id);

        if ($transaction && $transaction->user_id === Auth::id()) {
            return $transaction;
        }

        return null;
    }

    public function updateTransaction(int $id, array $data): bool
    {
        $transaction = $this->getTransaction($id);
        if (!$transaction) {
            return false;
        }

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
        if (!$transaction) {
            return false;
        }

        return $this->transactionRepository->delete($id);
    }
}
