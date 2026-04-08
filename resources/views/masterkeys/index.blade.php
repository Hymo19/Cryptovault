@extends('layouts.app')

@section('title', 'Master Keys')
@section('page-title', 'Master Keys')

@section('content')

{{-- Success --}}
@if(session('success'))
<div class="bg-green-900/40 border border-green-700 text-green-400 text-sm rounded-lg px-4 py-3 mb-6">
    {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-400">Gestion des clés de chiffrement maîtresses</p>
    <form method="POST" action="{{ route('masterkeys.rotate') }}"
        onsubmit="return confirm('Confirmer la rotation ? Les données chiffrées avec l\'ancienne clé restent accessibles.')">
        @csrf
        <button type="submit"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i class="fa-solid fa-rotate"></i>
            Rotation de la Master Key
        </button>
    </form>
</div>

{{-- Clé active --}}
<div class="bg-gray-900 border border-indigo-800/50 rounded-xl p-6 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-indigo-900/50 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-shield-halved text-indigo-400"></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-white">Master Key Active</h2>
            <p class="text-xs text-gray-500">Utilisée pour chiffrer toutes les nouvelles DEK</p>
        </div>
        <span class="ml-auto text-xs px-2 py-1 rounded-full bg-green-900/50 text-green-400">
            Active
        </span>
    </div>

    @if($activeMasterKey)
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <code class="flex-1 bg-gray-800 text-indigo-300 text-xs px-4 py-3 rounded-lg font-mono truncate">
                {{ substr($activeMasterKey->key_value, 0, 32) }}••••••••••••••••••••••••••••••••
            </code>
            <div class="flex-shrink-0 w-8 h-8 bg-green-900/50 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-lock text-green-400 text-xs"></i>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-xs text-gray-500">
            <span>ID : <span class="text-white">#{{ $activeMasterKey->id }}</span></span>
            <span>Créée le : <span class="text-white">{{ $activeMasterKey->created_at->format('d/m/Y à H:i') }}</span></span>
        </div>
    </div>
    @else
    <p class="text-sm text-red-400">Aucune master key active — veuillez en initialiser une.</p>
    @endif
</div>

{{-- Historique --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl">
    <div class="px-6 py-4 border-b border-gray-800">
        <h2 class="text-sm font-semibold text-white">Historique des Master Keys</h2>
    </div>

    @if($masterKeys->isEmpty())
    <div class="py-12 text-center">
        <i class="fa-solid fa-shield-halved text-gray-600 text-2xl mb-3"></i>
        <p class="text-sm text-gray-500">Aucune master key</p>
    </div>
    @else
    <div class="divide-y divide-gray-800">
        @foreach($masterKeys as $key)
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                    {{ $key->is_active ? 'bg-green-900/50' : 'bg-gray-800' }}">
                    <i class="fa-solid fa-key text-xs
                        {{ $key->is_active ? 'text-green-400' : 'text-gray-500' }}"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Master Key #{{ $key->id }}</p>
                    <p class="text-xs text-gray-500">{{ $key->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full
                {{ $key->is_active ? 'bg-green-900/50 text-green-400' : 'bg-gray-800 text-gray-500' }}">
                {{ $key->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Info box --}}
<div class="mt-6 bg-yellow-900/20 border border-yellow-800/50 rounded-xl px-5 py-4">
    <div class="flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-yellow-400 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-yellow-300 mb-1">Comment fonctionne l'Envelope Encryption ?</p>
            <p class="text-xs text-gray-400 leading-relaxed">
                Chaque donnée est chiffrée avec une clé unique (DEK) générée aléatoirement.
                Cette DEK est ensuite chiffrée par la Master Key (KEK).
                En cas de rotation, l'ancienne Master Key reste disponible pour déchiffrer
                les données existantes. Seules les nouvelles données utilisent la nouvelle clé.
            </p>
        </div>
    </div>
</div>

@endsection