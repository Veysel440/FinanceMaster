<?php

namespace App\Repositories;

use App\Interface\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getByUserId(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::where('user_id', $userId)->with('category');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->latest()->paginate(10);
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Transaction::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Transaction::destroy($id) > 0;
    }
}
