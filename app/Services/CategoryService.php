<?php

namespace App\Services;

use App\Interface\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getUserCategories(bool $includeDefault = true)
    {
        return $this->categoryRepository->getByUserId(Auth::id(), $includeDefault);
    }

    public function createCategory(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->categoryRepository->create($data);
    }

    public function getCategory(int $id)
    {
        $category = $this->categoryRepository->findById($id);
        if ($category && ($category->user_id === Auth::id() || $category->is_default)) {
            return $category;
        }
        return null;
    }

    public function updateCategory(int $id, array $data): bool
    {
        $category = $this->getCategory($id);
        if (!$category || $category->is_default) {
            return false;
        }
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategory($id);
        if (!$category || $category->is_default || $this->categoryRepository->hasTransactions($id)) {
            return false;
        }
        return $this->categoryRepository->delete($id);
    }
}
