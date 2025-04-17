<?php

namespace App\Repositories;

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
        return User::where('id', $id)->update($data);
    }

    public function updateProfilePhoto(int $id, string $path): bool
    {
        return User::where('id', $id)->update(['profile_photo' => $path]);
    }

    public function deleteProfilePhoto(int $id): bool
    {
        $user = User::find($id);
        if ($user && $user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            return User::where('id', $id)->update(['profile_photo' => null]);
        }
        return false;
    }
}
