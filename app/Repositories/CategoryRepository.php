<?php

namespace App\Repositories;

use App\Interface\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getByUserId(int $userId, bool $includeDefault = true): Collection
    {
        $query = Category::query();

        if ($includeDefault) {
            $query->where('user_id', $userId)->orWhere('is_default', true);
        } else {
            $query->where('user_id', $userId);
        }

        return $query->get();
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
        return Category::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Category::destroy($id) > 0;
    }

    public function hasTransactions(int $id): bool
    {
        return Category::where('id', $id)->has('transactions')->exists();
    }
}
