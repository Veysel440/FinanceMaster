<?php

namespace App\Repositories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Collection;

class GoalRepository implements GoalRepositoryInterface
{
    public function getByUserId(int $userId): Collection
    {
        return Goal::where('user_id', $userId)->get();
    }

    public function create(array $data): Goal
    {
        return Goal::create($data);
    }

    public function findById(int $id): ?Goal
    {
        return Goal::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Goal::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Goal::destroy($id) > 0;
    }
}
