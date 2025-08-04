<?php

namespace App\Interface;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getByUserId(int $userId, bool $includeDefault = true): Collection;

    public function create(array $data): Category;

    public function findById(int $id): ?Category;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function hasTransactions(int $id): bool;
}
