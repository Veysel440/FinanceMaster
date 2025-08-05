<?php

namespace App\Repositories;

use App\Interface\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getByUserId(int $userId, bool $includeDefault = true): Collection
    {
        return Category::when(!$includeDefault, fn($q) => $q->where('user_id', $userId))
            ->when($includeDefault, fn($q) => $q->where(function($q2) use ($userId) {
                $q2->where('user_id', $userId)->orWhere('is_default', true);
            }))
            ->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Category::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return Category::destroy($id) > 0;
    }

    public function hasTransactions(int $id): bool
    {
        return Category::find($id)?->transactions()->exists() ?? false;
    }
}
