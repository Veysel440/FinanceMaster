<?php

namespace App\Services;

use App\Interface\GoalRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class GoalService
{
    public function __construct(
        protected GoalRepositoryInterface $goalRepository
    ) {}

    public function getUserGoals()
    {
        return $this->goalRepository->getByUserId(Auth::id());
    }

    public function createGoal(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['current_amount'] = $data['current_amount'] ?? 0;
        return $this->goalRepository->create($data);
    }

    public function getGoal(int $id)
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
        if ($goal->target_amount <= 0) {
            return ['progress' => 0, 'message' => 'Hedef miktarı geçerli değil.'];
        }
        $progress = ($goal->current_amount / $goal->target_amount) * 100;
        $progress = min($progress, 100);

        return [
            'progress' => round($progress, 2),
            'message'  => $progress >= 100 ? 'Hedef tamamlandı!' : 'Hedefe ulaşmak için devam edin.',
        ];
    }
}
