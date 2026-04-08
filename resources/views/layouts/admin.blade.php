<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CryptoVault — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-950 text-white min-h-screen flex">

    <aside class="w-64 bg-gray-900 border-r border-gray-800 min-h-screen flex flex-col fixed">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-xl font-bold text-red-400">⚙️ CryptoVault</h1>
            <span class="text-xs bg-red-900 text-red-300 px-2 py-0.5 rounded-full mt-1 inline-block">
                Super Admin
            </span>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
                <i class="fas fa-chart-line w-5"></i> Vue globale
            </a>
            <a href="{{ route('admin.keys.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.keys.*') ? 'bg-red-700 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
                <i class="fas fa-key w-5"></i> Master Keys
            </a>
            <a href="{{ route('admin.tenants.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.tenants.*') ? 'bg-red-700 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
                <i class="fas fa-building w-5"></i> Tenants
            </a>
            <a href="{{ route('admin.plans.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.plans.*') ? 'bg-red-700 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
                <i class="fas fa-tags w-5"></i> Plans
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <p class="text-xs text-gray-500 mb-2">{{ Auth::user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-red-400 hover:text-red-300 text-sm flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-64 flex-1 p-8">
        @if(session('success'))
            <div class="bg-emerald-900 border border-emerald-700 text-emerald-300 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-900 border border-red-700 text-red-300 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>