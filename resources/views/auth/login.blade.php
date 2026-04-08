@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-900 to-indigo-900">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">CryptoVault</h1>
            <p class="text-gray-600 mt-2">Sécurisation des données bancaires</p>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-indigo-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                </label>
                <a href="#" class="text-sm text-indigo-600 hover:underline">Mot de passe oublié ?</a>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-2xl transition text-lg">
                Se connecter
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-8">
            Pas encore de compte ? 
            <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Créer un compte entreprise</a>
        </p>
    </div>
</div>
@endsection