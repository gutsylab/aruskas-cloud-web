@props(['tenant'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tenant->name ?? 'Tenant' }} - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}" class="text-xl font-semibold">{{ $tenant->name }}</a>
                    
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}" 
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            Dashboard
                        </a>
                        
                        <div class="relative">
                            <button type="button" 
                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center {{ request()->routeIs('cash.*') ? 'text-indigo-600 bg-indigo-50' : '' }}" 
                                    id="cash-menu-button">
                                Cash Management
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50" id="cash-dropdown">
                                <div class="py-1">
                                    <a href="{{ route('cash.categories.index', ['tenant_id' => $tenant->tenant_id]) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cash Categories</a>
                                    <a href="{{ route('cash.categories.trashed', ['tenant_id' => $tenant->tenant_id]) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Trashed Categories</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <a href="{{ route('profile', ['tenant_id' => $tenant->tenant_id]) }}" class="text-indigo-600 hover:text-indigo-500">Profile</a>
                    <form method="POST" action="{{ route('logout', ['tenant_id' => $tenant->tenant_id]) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-500">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Header -->
        @isset($header)
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <!-- Page Content -->
        {{ $slot }}
    </div>

    <script>
        // Simple dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const cashMenuButton = document.getElementById('cash-menu-button');
            const cashDropdown = document.getElementById('cash-dropdown');
            
            if (cashMenuButton && cashDropdown) {
                cashMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    cashDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!cashMenuButton.contains(e.target) && !cashDropdown.contains(e.target)) {
                        cashDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
