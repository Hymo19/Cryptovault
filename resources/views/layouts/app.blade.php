<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoVault — @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-950 text-white min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-64 bg-gray-900 border-r border-gray-800 flex flex-col z-50">
        
        {{-- Logo --}}
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-lock text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-white">CryptoVault</span>
            </div>
            <p class="text-xs text-gray-500 mt-1 ml-12">{{ Auth::user()->tenant->name }}</p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                <i class="fa-solid fa-gauge-high w-4"></i>
                Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fa-solid fa-cubes w-4"></i>
                Applications
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fa-solid fa-key w-4"></i>
                Clés API
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fa-solid fa-shield-halved w-4"></i>
                Master Keys
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fa-solid fa-list-check w-4"></i>
                Activité
            </a>
        </nav>

        {{-- User + Logout --}}
        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-red-900 hover:text-red-400 transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="ml-64 min-h-screen">
        
        {{-- Topbar --}}
        <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold text-white">@yield('page-title')</h1>
            <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-1 rounded-full 
                    {{ Auth::user()->tenant->status === 'trial' ? 'bg-yellow-900 text-yellow-400' : 'bg-green-900 text-green-400' }}">
                    {{ ucfirst(Auth::user()->tenant->status) }}
                </span>
            </div>
        </header>

        {{-- Page content --}}
        <div class="p-6">
            @yield('content')
        </div>
    </main>

</body>
</html>