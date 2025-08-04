<?php

namespace App\Repositories;

use App\Interface\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        return $user ? $user->update($data) : false;
    }

    public function updateProfilePhoto(int $id, string $path): bool
    {
        $user = $this->findById($id);
        if (!$user) return false;

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = $path;
        return $user->save();
    }

    public function deleteProfilePhoto(int $id): bool
    {
        $user = $this->findById($id);
        if ($user && $user->profile_photo) {
            if (Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = null;
            return $user->save();
        }
        return false;
    }
}
