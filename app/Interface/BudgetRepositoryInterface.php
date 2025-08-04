<?php

namespace App\Interface;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetRepositoryInterface
{
    public function getByUserId(int $userId): Collection;
    public function create(array $data): Budget;
    public function findById(int $id): ?Budget;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getSpentAmount(int $categoryId, string $month): float;
}
