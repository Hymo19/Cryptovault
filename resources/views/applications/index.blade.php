@extends('layouts.app')

@section('title', 'Applications')
@section('page-title', 'Applications')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-400">{{ $applications->count() }} application(s) enregistrée(s)</p>
    <a href="{{ route('applications.create') }}"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        <i class="fa-solid fa-plus"></i>
        Nouvelle application
    </a>
</div>

{{-- Success --}}
@if(session('success'))
<div class="bg-green-900/40 border border-green-700 text-green-400 text-sm rounded-lg px-4 py-3 mb-6">
    {{ session('success') }}
</div>
@endif

{{-- Liste --}}
@if($applications->isEmpty())
<div class="bg-gray-900 border border-gray-800 rounded-xl flex flex-col items-center justify-center py-24 text-center">
    <div class="w-14 h-14 bg-gray-800 rounded-xl flex items-center justify-center mb-4">
        <i class="fa-solid fa-cubes text-gray-600 text-xl"></i>
    </div>
    <p class="text-gray-400 font-medium mb-1">Aucune application</p>
    <p class="text-sm text-gray-600 mb-6">Créez votre première application pour commencer à chiffrer</p>
    <a href="{{ route('applications.create') }}"
        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
        + Créer une application
    </a>
</div>

@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($applications as $app)
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-indigo-700 transition group">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-900/50 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-cube text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $app->name }}</h3>
                    <p class="text-xs text-gray-500">Créée le {{ $app->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full 
                {{ $app->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                {{ ucfirst($app->status) }}
            </span>
        </div>

        @if($app->description)
        <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $app->description }}</p>
        @endif

        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                <p class="text-lg font-bold text-white">{{ number_format($app->total_encryptions) }}</p>
                <p class="text-xs text-gray-500">Chiffrements</p>
            </div>
            <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                <p class="text-lg font-bold text-white">{{ number_format($app->total_decryptions) }}</p>
                <p class="text-xs text-gray-500">Déchiffrements</p>
            </div>
        </div>

        <a href="{{ route('applications.show', $app) }}"
            class="block w-full text-center text-xs bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white font-medium py-2 rounded-lg transition">
            Voir les détails →
        </a>
    </div>
    @endforeach
</div>
@endif

@endsection