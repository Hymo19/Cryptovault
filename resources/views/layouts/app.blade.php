<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CryptoVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white min-h-screen flex flex-col fixed">
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-emerald-400">🔐 CryptoVault</h1>
            <p class="text-xs text-gray-400 mt-1">{{ Auth::user()->tenant->name }}</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <i class="fas fa-chart-line w-5"></i> Vue d'ensemble
            </a>
            <a href="{{ route('applications.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('applications.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <i class="fas fa-plug w-5"></i> Applications
            </a>
            <a href="{{ route('keys.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('keys.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <i class="fas fa-key w-5"></i> Clés API
            </a>
            <a href="{{ route('logs.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('logs.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <i class="fas fa-list w-5"></i> Logs d'activité
            </a>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <p class="text-xs text-gray-400 mb-1">{{ Auth::user()->name }}</p>
            <p class="text-xs text-emerald-400 mb-3">{{ Auth::user()->tenant->subscription->plan->name ?? 'Free' }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-2 text-red-400 hover:text-red-300 text-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENU PRINCIPAL -->
    <main class="ml-64 flex-1 p-8">
        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-800 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>