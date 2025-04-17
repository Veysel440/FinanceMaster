<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function update(int $id, array $data): bool;

    public function updateProfilePhoto(int $id, string $path): bool;

    public function deleteProfilePhoto(int $id): bool;
}
