<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Finance App') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-gray-800">Finance App</span>
                </a>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('dashboard') }}" class="border-b-2 {{ Route::is('dashboard') ? 'border-indigo-500' : 'border-transparent' }} text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">{{ __('Dashboard') }}</a>
                    <a href="{{ route('transactions.index') }}" class="border-b-2 {{ Route::is('transactions.*') ? 'border-indigo-500' : 'border-transparent' }} text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">{{ __('Transactions') }}</a>
                    <a href="{{ route('budgets.index') }}" class="border-b-2 {{ Route::is('budgets.*') ? 'border-indigo-500' : 'border-transparent' }} text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">{{ __('Budgets') }}</a>
                    <a href="{{ route('goals.index') }}" class="border-b-2 {{ Route::is('goals.*') ? 'border-indigo-500' : 'border-transparent' }} text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">{{ __('Goals') }}</a>
                    <a href="{{ route('reports.index') }}" class="border-b-2 {{ Route::is('reports.*') ? 'border-indigo-500' : 'border-transparent' }} text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">{{ __('Reports') }}</a>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="ml-3 relative">
                    <span class="text-gray-900">{{ Auth::user()->name }}</span>
                    <div class="inline-flex ml-4 space-x-4">
                        <a href="{{ route('user.profile') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Profile') }}</a>
                        <a href="{{ route('user.settings') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Settings') }}</a>
                        <a href="{{ route('logout') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Logout') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    @yield('content')
</main>
</body>
</html>
