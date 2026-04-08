@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-900 to-indigo-900">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">CryptoVault</h1>
        <p class="text-center text-gray-600 mb-8">Sécurisez vos données bancaires en 1 clic</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise</label>
                <input type="text" name="company_name" required
                       class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Votre nom complet</label>
                <input type="text" name="name" required
                       class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email professionnel</label>
                <input type="email" name="email" required
                       class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                Créer mon compte entreprise
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Déjà inscrit ? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Se connecter</a>
        </p>
    </div>
</div>
@endsection