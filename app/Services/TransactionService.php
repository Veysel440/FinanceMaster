<?php

namespace App\Services;

use App\Interface\TransactionRepositoryInterface;
use App\Services\BudgetService;
use App\Services\FinancialLogger;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected BudgetService $budgetService,
        protected FinancialLogger $financialLogger
    ) {}

    public function getUserTransactions(array $filters = [])
    {
        return $this->transactionRepository->getByUserId(Auth::id(), $filters);
    }

    public function createTransaction(array $data)
    {
        $data['user_id'] = Auth::id();
        $transaction = $this->transactionRepository->create($data);

        $this->financialLogger->transactionCreated(
            $transaction->user_id,
            $transaction->id,
            $transaction->type,
            (float) $transaction->amount,
            $transaction->category_id,
        );

        if ($transaction->type === 'expense') {
            $this->budgetService->checkBudgetsForTransaction($transaction->user_id, $transaction);
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

        if ($updated) {
            $this->financialLogger->transactionUpdated(
                $transaction->user_id,
                $transaction->id,
                $data,
            );

            if (($data['type'] ?? null) === 'expense') {
                $updatedTransaction = $this->getTransaction($id);
                $this->budgetService->checkBudgetsForTransaction($updatedTransaction->user_id, $updatedTransaction);
            }
        }

        return $updated;
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = $this->getTransaction($id);
        if (!$transaction) return false;

        $deleted = $this->transactionRepository->delete($id);
        if ($deleted) {
            $this->financialLogger->transactionDeleted($transaction->user_id, $transaction->id);
        }
        return $deleted;
    }
}
