<?php

namespace App\Services;

use App\Interface\GoalRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class GoalService
{
    protected $goalRepository;

    public function __construct(GoalRepositoryInterface $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    public function getUserGoals(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->goalRepository->getByUserId(Auth::id());
    }

    public function createGoal(array $data): \App\Models\Goal
    {
        $data['user_id'] = Auth::id();
        return $this->goalRepository->create($data);
    }

    public function getGoal(int $id): ?\App\Models\Goal
    {
        $goal = $this->goalRepository->findById($id);
        return $goal && $goal->user_id === Auth::id() ? $goal : null;
    }

    public function updateGoal(int $id, array $data): bool
    {
        $goal = $this->getGoal($id);
        return $goal ? $this->goalRepository->update($id, $data) : false;
    }

    public function deleteGoal(int $id): bool
    {
        $goal = $this->getGoal($id);
        return $goal ? $this->goalRepository->delete($id) : false;
    }

    public function getGoalProgress(int $goalId): array
    {
        $goal = $this->getGoal($goalId);
        if (!$goal) {
            return ['progress' => 0, 'message' => 'Hedef bulunamadı.'];
        }

        $progress = ($goal->current_amount / $goal->target_amount) * 100;

        return [
            'progress' => round($progress, 2),
            'message' => $progress >= 100 ? 'Hedef tamamlandı!' : 'Hedefe ulaşmak için devam edin.',
        ];
    }
}
