<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
interface TransactionRepositoryInterface
{
    public function getByUserId(int $userId, array $filters = []): LengthAwarePaginator;

    public function create(array $data): Transaction;

    public function findById(int $id): ?Transaction;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
