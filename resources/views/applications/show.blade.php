@extends('layouts.app')

@section('title', $application->name)
@section('page-title', $application->name)

@section('content')

<a href="{{ route('applications.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-6 transition">
    <i class="fa-solid fa-arrow-left"></i>
    Retour aux applications
</a>

@if(session('success'))
<div class="bg-green-900/40 border border-green-700 text-green-400 text-sm rounded-lg px-4 py-3 mb-6">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos application --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ number_format($application->total_encryptions) }}</p>
                <p class="text-xs text-gray-500 mt-1">Chiffrements</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ number_format($application->total_decryptions) }}</p>
                <p class="text-xs text-gray-500 mt-1">Déchiffrements</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ $apiKeys->where('status', 'active')->count() }}</p>
                <p class="text-xs text-gray-500 mt-1">Clés actives</p>
            </div>
        </div>

        {{-- Clés API --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="text-sm font-semibold text-white">Clés API</h2>
                <form method="POST" action="{{ route('apikeys.generate', $application) }}">
                    @csrf
                    <button type="submit"
                        class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg transition">
                        + Générer une clé
                    </button>
                </form>
            </div>

            @if($apiKeys->isEmpty())
            <div class="py-12 text-center">
                <i class="fa-solid fa-key text-gray-600 text-2xl mb-3"></i>
                <p class="text-sm text-gray-500">Aucune clé API</p>
            </div>
            @else
            <div class="divide-y divide-gray-800">
                @foreach($apiKeys as $key)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs px-2 py-1 rounded-full 
                            {{ $key->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                            {{ ucfirst($key->status) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $key->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 bg-gray-800 text-indigo-300 text-xs px-3 py-2 rounded-lg font-mono truncate">
                            {{ $key->key }}
                        </code>
                        <button onclick="navigator.clipboard.writeText('{{ $key->key }}')"
                            class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-lg transition"
                            title="Copier">
                            <i class="fa-solid fa-copy text-xs"></i>
                        </button>
                        @if($key->status === 'active')
                        <form method="POST" action="{{ route('apikeys.revoke', $key) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="p-2 bg-gray-800 hover:bg-red-900 text-gray-400 hover:text-red-400 rounded-lg transition"
                                title="Révoquer">
                                <i class="fa-solid fa-ban text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar infos --}}
    <div class="space-y-4">

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-white mb-4">Informations</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Statut</p>
                    <span class="text-xs px-2 py-1 rounded-full mt-1 inline-block
                        {{ $application->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Créée le</p>
                    <p class="text-sm text-white mt-0.5">{{ $application->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                @if($application->last_used_at)
                <div>
                    <p class="text-xs text-gray-500">Dernière utilisation</p>
                    <p class="text-sm text-white mt-0.5">{{ $application->last_used_at->diffForHumans() }}</p>
                </div>
                @endif
                @if($application->description)
                <div>
                    <p class="text-xs text-gray-500">Description</p>
                    <p class="text-sm text-white mt-0.5">{{ $application->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="bg-gray-900 border border-red-900/50 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-red-400 mb-3">Zone dangereuse</h3>
            <p class="text-xs text-gray-500 mb-4">La suppression est irréversible. Toutes les clés API associées seront supprimées.</p>
            <form method="POST" action="{{ route('applications.destroy', $application) }}"
                onsubmit="return confirm('Supprimer cette application ?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full bg-red-900/50 hover:bg-red-800 text-red-400 hover:text-red-300 text-sm font-semibold py-2 rounded-lg transition">
                    Supprimer l'application
                </button>
            </form>
        </div>

    </div>
</div>

@endsection