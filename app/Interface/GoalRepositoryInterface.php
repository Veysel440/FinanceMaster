<?php

namespace App\Interface;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Collection;

interface GoalRepositoryInterface
{
    public function getByUserId(int $userId): Collection;
    public function create(array $data): Goal;
    public function findById(int $id): ?Goal;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
