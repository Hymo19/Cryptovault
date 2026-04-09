@extends('layouts.guest')

@section('content')
<div class="min-h-screen w-full flex">

    <!-- PARTIE GAUCHE -->
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 flex-col justify-between p-12">
        <div>
            <h1 class="text-3xl font-bold text-white">🔐 CryptoVault</h1>
            <p class="text-indigo-300 mt-2 text-sm">Plateforme de chiffrement bancaire</p>
        </div>

        <div class="space-y-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock text-emerald-400"></i>
                    </div>
                    <p class="text-white font-medium">Chiffrement AES-256</p>
                </div>
                <p class="text-gray-400 text-sm">Vos données bancaires protégées par les meilleurs standards cryptographiques.</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-violet-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-sync-alt text-violet-400"></i>
                    </div>
                    <p class="text-white font-medium">Rotation automatique des clés</p>
                </div>
                <p class="text-gray-400 text-sm">Re-chiffrement transparent sans interruption de service.</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-orange-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-orange-400"></i>
                    </div>
                    <p class="text-white font-medium">Logs & monitoring complet</p>
                </div>
                <p class="text-gray-400 text-sm">Traçabilité totale de toutes vos opérations cryptographiques.</p>
            </div>
        </div>

        <p class="text-gray-600 text-xs">© 2026 CryptoVault — Tous droits réservés</p>
    </div>

    <!-- PARTIE DROITE -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-950">
        <div class="w-full max-w-md">

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-white">Connexion</h2>
                <p class="text-gray-400 mt-2">Accédez à votre espace sécurisé</p>
            </div>

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                    @foreach($errors->all() as $error)
                        <p><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm text-gray-400 font-medium block mb-2">Email professionnel</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="vous@entreprise.com"
                               class="w-full bg-gray-900 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3 pl-11 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-400 font-medium block mb-2">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full bg-gray-900 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3 pl-11 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-600 bg-gray-900">
                        Se souvenir de moi
                    </label>
                    <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <p class="text-center text-gray-500 text-sm mt-6">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">
                    Créer un compte entreprise
                </a>
            </p>

        </div>
    </div>

</div>
@endsection