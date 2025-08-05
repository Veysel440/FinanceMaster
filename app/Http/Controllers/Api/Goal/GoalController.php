<?php

namespace App\Http\Controllers\Api\Goal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\StoreGoalRequest;
use App\Http\Requests\Goal\UpdateGoalRequest;
use App\Http\Resources\Goal\GoalResource;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;

class GoalController extends Controller
{
    public function __construct(
        protected GoalService $goalService
    ) {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $goals = $this->goalService->getUserGoals();
        return response()->json([
            'success' => true,
            'data'    => GoalResource::collection($goals),
        ]);
    }

    public function store(StoreGoalRequest $request): JsonResponse
    {
        $goal = $this->goalService->createGoal($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Hedef başarıyla eklendi.',
            'data'    => new GoalResource($goal),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $goal = $this->goalService->getGoal($id);
        if (!$goal) {
            return response()->json(['success' => false, 'message' => 'Hedef bulunamadı.'], 404);
        }
        return response()->json([
            'success' => true,
            'data'    => new GoalResource($goal),
        ]);
    }

    public function update(UpdateGoalRequest $request, $id): JsonResponse
    {
        $updated = $this->goalService->updateGoal($id, $request->validated());
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Hedef başarıyla güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Hedef güncellenemedi.'], 400);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->goalService->deleteGoal($id);
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Hedef başarıyla silindi.']);
        }
        return response()->json(['success' => false, 'message' => 'Hedef silinemedi.'], 400);
    }
}
