<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BudgetService;
use App\Services\CategoryService;


class BudgetController extends Controller
{
    protected $budgetService;
    protected $categoryService;

    public function __construct(BudgetService $budgetService, CategoryService $categoryService)
    {
        $this->middleware('auth');
        $this->budgetService = $budgetService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $budgets = $this->budgetService->getUserBudgets()->map(function ($budget) {
            $status = $this->budgetService->checkBudgetStatus($budget->id);
            $budget->status = $status;
            return $budget;
        });

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = $this->categoryService->getUserCategories();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|date_format:Y-m',
        ]);

        $validated['month'] = $validated['month'] . '-01';
        $this->budgetService->createBudget($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Bütçe başarıyla eklendi.');
    }

    public function edit($id)
    {
        $budget = $this->budgetService->getBudget($id);
        if (!$budget) {
            return redirect()->route('budgets.index')->with('error', 'Bütçe bulunamadı.');
        }

        $categories = $this->categoryService->getUserCategories();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|date_format:Y-m',
        ]);

        $validated['month'] = $validated['month'] . '-01';
        if ($this->budgetService->updateBudget($id, $validated)) {
            return redirect()->route('budgets.index')
                ->with('success', 'Bütçe başarıyla güncellendi.');
        }

        return redirect()->route('budgets.index')
            ->with('error', 'Bütçe güncellenemedi.');
    }

    public function destroy($id)
    {
        if ($this->budgetService->deleteBudget($id)) {
            return redirect()->route('budgets.index')
                ->with('success', 'Bütçe başarıyla silindi.');
        }

        return redirect()->route('budgets.index')
            ->with('error', 'Bütçe silinemedi.');
    }
}
