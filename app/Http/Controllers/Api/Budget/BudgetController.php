<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Http\Resources\Budget\BudgetResource;
use App\Services\BudgetService;
use Illuminate\Http\JsonResponse;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $budgets = $this->budgetService->getUserBudgets()->map(function ($budget) {
            $status = $this->budgetService->checkBudgetStatus($budget->id);
            $budget->status = $status;
            return $budget;
        });

        return response()->json([
            'success' => true,
            'data' => BudgetResource::collection($budgets),
        ]);
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $budget = $this->budgetService->createBudget($request->validatedWithMonth());
        return response()->json([
            'success' => true,
            'message' => 'Bütçe başarıyla eklendi.',
            'data'    => new BudgetResource($budget),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $budget = $this->budgetService->getBudget($id);
        if (!$budget) {
            return response()->json(['success' => false, 'message' => 'Bütçe bulunamadı.'], 404);
        }

        $status = $this->budgetService->checkBudgetStatus($budget->id);
        $budget->status = $status;

        return response()->json([
            'success' => true,
            'data' => new BudgetResource($budget),
        ]);
    }

    public function update(UpdateBudgetRequest $request, $id): JsonResponse
    {
        $updated = $this->budgetService->updateBudget($id, $request->validatedWithMonth());
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Bütçe güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Bütçe güncellenemedi.'], 400);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->budgetService->deleteBudget($id);
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Bütçe silindi.']);
        }
        return response()->json(['success' => false, 'message' => 'Bütçe silinemedi.'], 400);
    }
}
