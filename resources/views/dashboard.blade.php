@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">
            Bienvenue, {{ Auth::user()->name }} 👋
        </h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-700 font-medium">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8">
        <h2 class="text-2xl font-semibold mb-6">🧪 Test chiffrement (rapport de stage)</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- CHIFFRER -->
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="font-semibold mb-4 text-lg">1. Chiffrer un numéro de carte</h3>
                <input id="cardInput" type="text" value="4532123456789123"
                       class="w-full px-4 py-3 border rounded-2xl mb-4 font-mono">
                <button onclick="encryptCard()"
                        class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-semibold hover:bg-emerald-700 transition">
                    🔐 CHIFFRER
                </button>
                <pre id="encryptResult" class="mt-4 text-xs bg-white border p-4 rounded-2xl max-h-60 overflow-auto"></pre>
            </div>

            <!-- DÉCHIFFRER -->
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="font-semibold mb-4 text-lg">2. Déchiffrer (auto-rempli après chiffrement)</h3>
                <input id="encryptedInput" placeholder="encrypted (rempli auto)"
                       class="w-full px-4 py-3 border rounded-2xl mb-3 font-mono text-xs">
                <input id="dekInput" placeholder="encrypted_dek (rempli auto)"
                       class="w-full px-4 py-3 border rounded-2xl mb-3 font-mono text-xs">
                <div class="flex gap-3 mb-4">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Version</label>
                        <input id="versionInput" type="number" value="1"
                               class="w-full px-4 py-3 border rounded-2xl font-mono">
                    </div>
                </div>
                <button onclick="decryptCard()"
                        class="w-full bg-violet-600 text-white py-4 rounded-2xl font-semibold hover:bg-violet-700 transition">
                    🔓 DÉCHIFFRER (masqué)
                </button>
                <pre id="decryptResult" class="mt-4 text-xs bg-white border p-4 rounded-2xl"></pre>
            </div>
        </div>
    </div>
</div>

<script>
async function encryptCard() {
    const data = document.getElementById('cardInput').value;
    const resultDiv = document.getElementById('encryptResult');
    resultDiv.textContent = '⏳ Chiffrement en cours...';

    try {
        const res = await fetch('/api/encrypt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ data })
        });

        const json = await res.json();
        resultDiv.textContent = JSON.stringify(json, null, 2);

        if (json.success) {
            // ← AUTO-REMPLISSAGE des champs déchiffrement
            document.getElementById('encryptedInput').value = json.encrypted;
            document.getElementById('dekInput').value = json.encrypted_dek;
            document.getElementById('versionInput').value = json.version; // ← version correcte auto
        }
    } catch (e) {
        resultDiv.textContent = 'ERREUR : ' + e.message;
    }
}

async function decryptCard() {
    const encrypted     = document.getElementById('encryptedInput').value;
    const encrypted_dek = document.getElementById('dekInput').value;
    const version       = parseInt(document.getElementById('versionInput').value);
    const resultDiv     = document.getElementById('decryptResult');
    resultDiv.textContent = '⏳ Déchiffrement en cours...';

    try {
        const res = await fetch('/api/decrypt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ encrypted, encrypted_dek, version })
        });

        const json = await res.json();
        resultDiv.textContent = JSON.stringify(json, null, 2);
    } catch (e) {
        resultDiv.textContent = 'ERREUR : ' + e.message;
    }
}
</script>
@endsection