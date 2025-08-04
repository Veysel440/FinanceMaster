<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfilePhotoRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Kullanıcı profilini getirir.
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Profil güncelleme
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $result = $this->userService->updateProfile($userId, $request->validated());

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Profil güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Profil güncellenemedi.'], 400);
    }

    /**
     * Kullanıcı ayarları getirir.
     */
    public function settings(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => [
                'currency' => $user->currency,
                'locale' => $user->locale,
            ],
        ]);
    }

    /**
     * Ayarları güncelle
     */
    public function updateSettings(UpdateSettingsRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $result = $this->userService->updateSettings($userId, $request->validated());

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Ayarlar güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Ayarlar güncellenemedi.'], 400);
    }

    /**
     * Profil fotoğrafı güncelle
     */
    public function updateProfilePhoto(UpdateProfilePhotoRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $file = $request->file('profile_photo');

        $result = $this->userService->updateProfilePhoto($userId, $file);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Profil fotoğrafı güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'Profil fotoğrafı güncellenemedi.'], 400);
    }

    /**
     * Profil fotoğrafı sil
     */
    public function deleteProfilePhoto(): JsonResponse
    {
        $userId = Auth::id();
        $result = $this->userService->deleteProfilePhoto($userId);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Profil fotoğrafı silindi.']);
        }
        return response()->json(['success' => false, 'message' => 'Profil fotoğrafı silinemedi.'], 400);
    }
}
