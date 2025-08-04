<?php

namespace App\Services;

use App\Interface\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getUser(int $id): ?\App\Models\User
    {
        $user = $this->userRepository->findById($id);
        return ($user && $user->id === Auth::id()) ? $user : null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $user = $this->getUser($id);
        if (!$user) return false;

        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function updateSettings(int $id, array $data): bool
    {
        $user = $this->getUser($id);
        return $user ? $this->userRepository->update($id, $data) : false;
    }

    public function updateProfilePhoto(int $id, $file): bool
    {
        $user = $this->getUser($id);
        if (!$user) return false;

        $path = $file->store('profile-photos', 'public');
        return $this->userRepository->updateProfilePhoto($id, $path);
    }

    public function deleteProfilePhoto(int $id): bool
    {
        $user = $this->getUser($id);
        return $user ? $this->userRepository->deleteProfilePhoto($id) : false;
    }
}
