<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth');
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getUserCategories(false);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . auth()->id(),
        ]);

        $this->categoryService->createCategory($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla eklendi.');
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategory($id);
        if (!$category || $category->is_default) {
            return redirect()->route('categories.index')->with('error', 'Kategori bulunamadı veya düzenlenemez.');
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id . ',id,user_id,' . auth()->id(),
        ]);

        if ($this->categoryService->updateCategory($id, $validated)) {
            return redirect()->route('categories.index')
                ->with('success', 'Kategori başarıyla güncellendi.');
        }

        return redirect()->route('categories.index')
            ->with('error', 'Kategori güncellenemedi.');
    }

    public function destroy($id)
    {
        if ($this->categoryService->deleteCategory($id)) {
            return redirect()->route('categories.index')
                ->with('success', 'Kategori başarıyla silindi.');
        }

        return redirect()->route('categories.index')
            ->with('error', 'Kategori silinemedi. Varsayılan kategoriler veya işlemle ilişkili kategoriler silinemez.');
    }
}
