@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Profile') }}</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <!-- Profil Resmi -->
            <div class="mb-6">
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                        @if ($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="Profil Resmi" class="h-full w-full object-cover">
                        @else
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-4">
                        <form action="{{ route('user.profile-photo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" onchange="this.form.submit()">
                            <label for="profile_photo" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer">
                                {{ __('Upload Photo') }}
                            </label>
                        </form>
                        @if ($user->profile_photo)
                            <form action="{{ route('user.profile-photo.delete') }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Are you sure you want to delete the profile photo?') }}')">{{ __('Delete Photo') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
                @error('profile_photo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Profil Formu -->
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input type="email" name="email" id="email" value="{{ $user->email }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
