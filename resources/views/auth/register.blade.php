<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoVault — Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-md px-4">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-lock text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">CryptoVault</h1>
            <p class="text-gray-500 text-sm mt-1">Créez votre espace de chiffrement</p>
        </div>

        {{-- Card --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8">

            {{-- Erreurs --}}
            @if($errors->any())
                <div class="bg-red-900/40 border border-red-700 text-red-400 text-sm rounded-lg px-4 py-3 mb-6">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Nom de l'entreprise</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-building text-sm"></i>
                        </span>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                            placeholder="Ma Société">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Votre nom</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                            placeholder="John Doe">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Adresse email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                            placeholder="vous@exemple.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Confirmer le mot de passe</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-indigo-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm py-2.5 rounded-lg transition">
                    Créer mon compte
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition">Se connecter</a>
        </p>

    </div>

</body>
</html>