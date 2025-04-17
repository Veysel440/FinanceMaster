<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser(int $id): ?\App\Models\User
    {
        $user = $this->userRepository->findById($id);
        return $user && $user->id === Auth::id() ? $user : null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $user = $this->getUser($id);
        if (!$user) {
            return false;
        }

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
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
        if (!$user) {
            return false;
        }


        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }


        $path = $file->store('profile-photos', 'public');
        return $this->userRepository->updateProfilePhoto($id, $path);
    }

    public function deleteProfilePhoto(int $id): bool
    {
        $user = $this->getUser($id);
        return $user ? $this->userRepository->deleteProfilePhoto($id) : false;
    }
}
