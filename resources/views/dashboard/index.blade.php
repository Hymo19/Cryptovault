@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Bannière trial --}}
@if(Auth::user()->tenant->status === 'trial')
<div class="bg-yellow-900/30 border border-yellow-700/50 rounded-xl px-5 py-4 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-clock text-yellow-400"></i>
        <p class="text-sm text-yellow-300">
            Période d'essai — expire le 
            <span class="font-semibold">{{ Auth::user()->tenant->trial_ends_at->format('d/m/Y') }}</span>
        </p>
    </div>
    <a href="#" class="text-xs bg-yellow-500 hover:bg-yellow-400 text-black font-semibold px-3 py-1.5 rounded-lg transition">
        Passer Pro
    </a>
</div>
@endif

{{-- Stats cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-400">Applications</span>
            <div class="w-8 h-8 bg-indigo-900/50 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-cubes text-indigo-400 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $tenant->applications->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">applications actives</p>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-400">Chiffrements</span>
            <div class="w-8 h-8 bg-green-900/50 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-lock text-green-400 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">
            {{ number_format($tenant->applications->sum('total_encryptions')) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">opérations totales</p>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-400">Déchiffrements</span>
            <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-lock-open text-blue-400 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">
            {{ number_format($tenant->applications->sum('total_decryptions')) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">opérations totales</p>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-400">Clés API</span>
            <div class="w-8 h-8 bg-purple-900/50 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-key text-purple-400 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">
            {{ $tenant->applications->sum(fn($app) => $app->apiKeys->where('status', 'active')->count()) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">clés actives</p>
    </div>

</div>

{{-- Contenu principal --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Applications récentes --}}
    <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
            <h2 class="text-sm font-semibold text-white">Applications</h2>
            <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300 transition">Voir tout →</a>
        </div>

        @if($tenant->applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fa-solid fa-cubes text-gray-600 text-lg"></i>
            </div>
            <p class="text-sm text-gray-500">Aucune application créée</p>
            <a href="#" class="mt-4 text-xs bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg transition">
                + Créer une application
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-800">
            @foreach($tenant->applications as $app)
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-900/40 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-cube text-indigo-400 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $app->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ number_format($app->total_encryptions) }} chiffrements
                        </p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full 
                    {{ $app->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                    {{ ucfirst($app->status) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Activité récente --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
            <h2 class="text-sm font-semibold text-white">Activité récente</h2>
            <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300 transition">Voir tout →</a>
        </div>

        @php
            $logs = $tenant->activityLogs()->latest('performed_at')->take(8)->get();
        @endphp

        @if($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="fa-solid fa-list-check text-gray-600 text-2xl mb-3"></i>
            <p class="text-sm text-gray-500">Aucune activité</p>
        </div>
        @else
        <div class="divide-y divide-gray-800">
            @foreach($logs as $log)
            <div class="px-6 py-3 flex items-start gap-3">
                <div class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $log->status === 'success' ? 'bg-green-900/50' : 'bg-red-900/50' }}">
                    <i class="text-xs {{ $log->status === 'success' ? 'fa-solid fa-check text-green-400' : 'fa-solid fa-xmark text-red-400' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-white capitalize">{{ str_replace('_', ' ', $log->action) }}</p>
                    <p class="text-xs text-gray-500">{{ $log->performed_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection