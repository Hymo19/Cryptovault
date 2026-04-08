@extends('layouts.admin')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Vue globale</h2>
        <p class="text-gray-400 mt-1">Supervision complète de CryptoVault</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800">
            <p class="text-xs text-gray-400">Tenants</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_tenants'] }}</p>
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
            <p class="text-xs text-gray-400">Rotations</p>
            <p class="text-3xl font-bold text-orange-400 mt-1">{{ $stats['total_rotations'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Statut clé active -->
        <div class="bg-gray-900 rounded-2xl p-6 border border-gray-800">
            <h3 class="font-semibold text-lg mb-4">🔑 Clé master active</h3>
            @if($stats['active_key'])
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-emerald-900 text-emerald-400 px-3 py-1 rounded-full text-sm">✅ Active</span>
                    <span class="text-gray-300">Version {{ $stats['active_key']->id }}</span>
                </div>
                <p class="text-sm text-gray-500">Créée le {{ $stats['active_key']->created_at?->format('d/m/Y à H:i') ?? '—' }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['total_keys'] }} version(s) au total</p>
                <a href="{{ route('admin.keys.index') }}"
                   class="mt-4 inline-block bg-orange-700 hover:bg-orange-600 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                    <i class="fas fa-sync-alt mr-1"></i> Gérer les clés
                </a>
            @else
                <p class="text-red-400">Aucune clé active.</p>
            @endif
        </div>

        <!-- Logs récents -->
        <div class="bg-gray-900 rounded-2xl p-6 border border-gray-800">
            <h3 class="font-semibold text-lg mb-4">📋 Activité récente</h3>
            @forelse($stats['recent_logs'] as $log)
                <div class="flex items-center justify-between py-2 border-b border-gray-800 last:border-0">
                    <div>
                        <p class="text-sm font-medium capitalize text-gray-200">{{ $log->action }}</p>
                        <p class="text-xs text-gray-500">{{ $log->tenant->name ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full {{ $log->status === 'success' ? 'bg-emerald-900 text-emerald-400' : 'bg-red-900 text-red-400' }}">
                            {{ $log->status }}
                        </span>
                        <p class="text-xs text-gray-600 mt-1">{{ $log->performed_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Aucune activité.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection