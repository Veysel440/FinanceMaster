<?php

namespace App\Services\Contracts;

use App\Models\User;

interface UserServiceInterface
{
    public function getUser(int $id): ?User;

    public function updateProfile(int $id, array $data): bool;

    public function updateSettings(int $id, array $data): bool;

    public function updateProfilePhoto(int $id, $file): bool;

    public function deleteProfilePhoto(int $id): bool;
}
