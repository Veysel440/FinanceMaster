<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getUserCategories();
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla eklendi.',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori bulunamadı.'], 404);
        }
        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        $updated = $this->categoryService->updateCategory($id, $request->validated());
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Kategori güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Kategori güncellenemedi.'], 400);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($id);
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Kategori silindi.']);
        }
        return response()->json(['success' => false, 'message' => 'Kategori silinemedi. Varsayılan veya ilişkili kategoriler silinemez.'], 400);
    }
}
