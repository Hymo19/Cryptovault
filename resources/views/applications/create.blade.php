@extends('layouts.app')

@section('title', 'Nouvelle application')
@section('page-title', 'Nouvelle application')

@section('content')

<div class="max-w-xl">

    <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-6 transition">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux applications
    </a>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">

        <h2 class="text-lg font-semibold text-white mb-6">Créer une application</h2>

        @if($errors->any())
        <div class="bg-red-900/40 border border-red-700 text-red-400 text-sm rounded-lg px-4 py-3 mb-6">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('applications.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Nom de l'application <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                    placeholder="Mon Application">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Description <span class="text-gray-600">(optionnel)</span></label>
                <textarea name="description" rows="3"
                    class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:border-indigo-500 transition resize-none"
                    placeholder="Décrivez l'utilisation de cette application...">{{ old('description') }}</textarea>
            </div>

            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-lg px-4 py-3">
                <div class="flex items-center gap-2 text-indigo-300 text-sm">
                    <i class="fa-solid fa-circle-info text-indigo-400"></i>
                    Une clé API sera générée automatiquement à la création.
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm py-2.5 rounded-lg transition">
                    Créer l'application
                </button>
                <a href="{{ route('applications.index') }}"
                    class="flex-1 text-center bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold text-sm py-2.5 rounded-lg transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection