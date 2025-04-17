@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Yeni Hedef Ekle</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('goals.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Başlık</label>
                        <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="target_amount" class="block text-sm font-medium text-gray-700">Hedef Miktar</label>
                        <input type="number" name="target_amount" id="target_amount" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('target_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="current_amount" class="block text-sm font-medium text-gray-700">Mevcut Miktar (Opsiyonel)</label>
                        <input type="number" name="current_amount" id="current_amount" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('current_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('end_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('goals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">İptal</a>
                    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
