@extends('layouts.admin')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.tenants.index') }}" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-bold">{{ $tenant->name }}</h2>
            <p class="text-gray-400 mt-1">{{ $tenant->email }}</p>
        </div>
        <span class="ml-auto px-3 py-1 rounded-full text-sm
            {{ $tenant->status === 'active' ? 'bg-emerald-900 text-emerald-400' :
               ($tenant->status === 'trial'  ? 'bg-blue-900 text-blue-400' : 'bg-red-900 text-red-400') }}">
            {{ ucfirst($tenant->status) }}
        </span>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800">
            <p class="text-xs text-gray-400">Applications</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_apps'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800">
            <p class="text-xs text-gray-400">Chiffrements</p>
            <p class="text-3xl font-bold text-emerald-400 mt-1">{{ number_format($stats['total_encryptions']) }}</p>
        </div>
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800">
            <p class="text-xs text-gray-400">Déchiffrements</p>
            <p class="text-3xl font-bold text-violet-400 mt-1">{{ number_format($stats['total_decryptions']) }}</p>
        </div>
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800">
            <p class="text-xs text-gray-400">Plan</p>
            <p class="text-2xl font-bold text-orange-400 mt-1">{{ $tenant->subscription->plan->name ?? 'Free' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Applications -->
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6">
            <h3 class="font-semibold text-lg mb-4">⚡ Applications</h3>
            @forelse($tenant->applications as $app)
                <div class="py-3 border-b border-gray-800 last:border-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-white">{{ $app->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $app->total_encryptions }} chiffrements</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $app->status === 'active' ? 'bg-emerald-900 text-emerald-400' : 'bg-red-900 text-red-400' }}">
                            {{ $app->status }}
                        </span>
                    </div>
                    <!-- API Keys -->
                    @foreach($app->apiKeys as $key)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs text-gray-500 font-mono">{{ Str::limit($key->key, 30) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $key->status === 'active' ? 'bg-emerald-900 text-emerald-400' : 'bg-gray-800 text-gray-500' }}">
                                {{ $key->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="text-gray-500 text-sm">Aucune application.</p>
            @endforelse
        </div>

        <!-- Actions -->
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6">
            <h3 class="font-semibold text-lg mb-4">⚙️ Actions</h3>

            @if($tenant->status !== 'suspended')
                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}"
                      onsubmit="return confirm('Suspendre ce tenant ?')">
                    @csrf
                    <button class="w-full bg-red-900 hover:bg-red-800 text-red-300 py-3 rounded-xl text-sm font-semibold transition mb-3">
                        <i class="fas fa-ban mr-2"></i> Suspendre le compte
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}">
                    @csrf
                    <button class="w-full bg-emerald-900 hover:bg-emerald-800 text-emerald-300 py-3 rounded-xl text-sm font-semibold transition mb-3">
                        <i class="fas fa-check mr-2"></i> Réactiver le compte
                    </button>
                </form>
            @endif

            <div class="mt-4 p-4 bg-gray-800 rounded-xl text-sm text-gray-400">
                <p><span class="text-gray-500">Inscrit le :</span> {{ $tenant->created_at->format('d/m/Y à H:i') }}</p>
                <p class="mt-1"><span class="text-gray-500">Email :</span> {{ $tenant->email }}</p>
            </div>
        </div>
    </div>

    <!-- Logs récents -->
    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6">
        <h3 class="font-semibold text-lg mb-4">📋 Activité récente</h3>
        <table class="w-full text-sm">
            <thead class="border-b border-gray-800">
                <tr class="text-gray-400">
                    <th class="pb-3 text-left">Action</th>
                    <th class="pb-3 text-left">Application</th>
                    <th class="pb-3 text-left">Statut</th>
                    <th class="pb-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recent_logs'] as $log)
                <tr class="border-b border-gray-800 last:border-0">
                    <td class="py-3 capitalize text-gray-200">{{ $log->action }}</td>
                    <td class="py-3 text-gray-400">{{ $log->application->name ?? '—' }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $log->status === 'success' ? 'bg-emerald-900 text-emerald-400' : 'bg-red-900 text-red-400' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-500">{{ $log->performed_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-6 text-center text-gray-500">Aucune activité.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection