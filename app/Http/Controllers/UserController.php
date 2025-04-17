<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($this->userService->updateProfile(Auth::id(), $validated)) {
            return redirect()->route('user.profile')->with('success', 'Profil başarıyla güncellendi.');
        }

        return redirect()->route('user.profile')->with('error', 'Profil güncellenemedi.');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('user.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|in:TRY,USD,EUR',
            'locale' => 'required|in:tr,en',
        ]);

        if ($this->userService->updateSettings(Auth::id(), $validated)) {
            return redirect()->route('user.settings')->with('success', 'Ayarlar başarıyla güncellendi.');
        }

        return redirect()->route('user.settings')->with('error', 'Ayarlar güncellenemedi.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($this->userService->updateProfilePhoto(Auth::id(), $request->file('profile_photo'))) {
            return redirect()->route('user.profile')->with('success', 'Profil resmi başarıyla güncellendi.');
        }

        return redirect()->route('user.profile')->with('error', 'Profil resmi güncellenemedi.');
    }

    public function deleteProfilePhoto()
    {
        if ($this->userService->deleteProfilePhoto(Auth::id())) {
            return redirect()->route('user.profile')->with('success', 'Profil resmi başarıyla silindi.');
        }

        return redirect()->route('user.profile')->with('error', 'Profil resmi silinemedi.');
    }
}
