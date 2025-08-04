<?php

namespace App\Repositories;

use App\Interface\BudgetRepositoryInterface;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class BudgetRepository implements BudgetRepositoryInterface
{
    public function getByUserId(int $userId): Collection
    {
        return Budget::where('user_id', $userId)->with('category')->get();
    }

    public function create(array $data): Budget
    {
        return Budget::create($data);
    }

    public function findById(int $id): ?Budget
    {
        return Budget::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Budget::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return Budget::destroy($id) > 0;
    }

    public function getSpentAmount(int $categoryId, string $month): float
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        return Transaction::where('category_id', $categoryId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
    }
}
