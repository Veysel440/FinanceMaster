@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Settings') }}</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('user.settings.update') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">{{ __('Currency') }}</label>
                        <select name="currency" id="currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="TRY" {{ $user->currency == 'TRY' ? 'selected' : '' }}>Türk Lirası (TRY)</option>
                            <option value="USD" {{ $user->currency == 'USD' ? 'selected' : '' }}>ABD Doları (USD)</option>
                            <option value="EUR" {{ $user->currency == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                        </select>
                        @error('currency')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="locale" class="block text-sm font-medium text-gray-700">{{ __('Language') }}</label>
                        <select name="locale" id="locale" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="tr" {{ $user->locale == 'tr' ? 'selected' : '' }}>Türkçe</option>
                            <option value="en" {{ $user->locale == 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        @error('locale')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
