@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Vue d'ensemble</h2>
        <p class="text-gray-500 mt-1">{{ Auth::user()->tenant->name }}</p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-gray-500">Applications</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_apps'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-gray-500">Chiffrements ce mois</p>
            <p class="text-3xl font-bold text-emerald-600">{{ number_format($stats['total_encryptions']) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-gray-500">Déchiffrements ce mois</p>
            <p class="text-3xl font-bold text-violet-600">{{ number_format($stats['total_decryptions']) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Plan actuel</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $tenant->subscription->plan->name ?? 'Free' }}</p>
                </div>
                @if($plan)
                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">
                        {{ number_format($stats['total_ops']) }} / {{ number_format($plan->max_ops_per_month) }} ops
                    </span>
                @endif
            </div>
            @if($plan)
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $stats['quota_percent'] > 80 ? 'bg-red-500' : 'bg-orange-500' }}"
                         style="width: {{ min(100, $stats['quota_percent']) }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['quota_percent'] }}% du quota utilisé</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Clés API rapides -->
        <div class="bg-white rounded-2xl shadow p-6 lg:col-span-1">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">🔑 Mes clés API</h3>
                <a href="{{ route('keys.index') }}" class="text-sm text-emerald-600 hover:underline">Gérer</a>
            </div>
            @forelse($stats['api_keys'] as $key)
                <div class="mb-3 p-3 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">{{ $key->application->name }}</p>
                    <div class="flex items-center gap-2">
                        <code class="text-xs text-gray-700 flex-1 truncate" id="key-{{ $key->id }}">
                            {{ substr($key->key, 0, 16) }}...
                        </code>
                        <button onclick="copyKey('{{ $key->key }}', {{ $key->id }})"
                                class="text-gray-400 hover:text-emerald-600 transition">
                            <i class="fas fa-copy text-xs"></i>
                        </button>
                        <button onclick="toggleKey({{ $key->id }}, '{{ $key->key }}')"
                                class="text-gray-400 hover:text-emerald-600 transition">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                    <span class="text-xs {{ $key->status === 'active' ? 'text-emerald-600' : 'text-red-500' }}">
                        ● {{ $key->status }}
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Aucune clé. <a href="{{ route('applications.index') }}" class="text-emerald-600">Créer une app</a></p>
            @endforelse
        </div>

        <!-- Testeur API intégré -->
        <div class="bg-white rounded-2xl shadow p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4">🧪 Testeur API</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500 font-medium block mb-2">Donnée à chiffrer</label>
                    <input id="testData" type="text" value="4532123456789123"
                           placeholder="Ex: numéro de carte"
                           class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <select id="testKey" class="w-full mt-2 px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Sélectionner une clé API</option>
                        @foreach($stats['api_keys'] as $key)
                            <option value="{{ $key->key }}">{{ $key->application->name }} — {{ substr($key->key, 0, 20) }}...</option>
                        @endforeach
                    </select>
                    <button onclick="testEncrypt()"
                            class="w-full mt-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-sm font-semibold transition">
                        🔐 Chiffrer
                    </button>
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-medium block mb-2">Résultat</label>
                    <div id="testResult" class="w-full h-32 bg-gray-50 border rounded-xl p-3 text-xs font-mono text-gray-600 overflow-auto">
                        Le résultat apparaîtra ici...
                    </div>
                    <button onclick="testDecrypt()"
                            id="decryptBtn" disabled
                            class="w-full mt-2 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white py-3 rounded-xl text-sm font-semibold transition">
                        🔓 Déchiffrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Applications récentes -->
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">⚡ Mes applications</h3>
                <a href="{{ route('applications.index') }}" class="text-sm text-emerald-600 hover:underline">Voir tout</a>
            </div>
            @forelse($stats['recent_apps'] as $app)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium text-gray-800">{{ $app->name }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($app->total_encryptions) }} chiffrements</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full {{ $app->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $app->status }}
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Aucune application. <a href="{{ route('applications.index') }}" class="text-emerald-600 hover:underline">Créer une app</a></p>
            @endforelse
        </div>

        <!-- Logs récents -->
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">📋 Activité récente</h3>
                <a href="{{ route('logs.index') }}" class="text-sm text-emerald-600 hover:underline">Voir tout</a>
            </div>
            @forelse($stats['recent_logs'] as $log)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div class="flex items-center gap-3">
                        @php
                            $colors = [
                                'encrypt'      => 'bg-emerald-100 text-emerald-700',
                                'decrypt'      => 'bg-violet-100 text-violet-700',
                                'login'        => 'bg-blue-100 text-blue-700',
                                'create_app'   => 'bg-teal-100 text-teal-700',
                                'generate_key' => 'bg-indigo-100 text-indigo-700',
                                'revoke_key'   => 'bg-pink-100 text-pink-700',
                            ];
                            $icons = [
                                'encrypt'      => 'fa-lock',
                                'decrypt'      => 'fa-lock-open',
                                'login'        => 'fa-sign-in-alt',
                                'create_app'   => 'fa-plus-circle',
                                'generate_key' => 'fa-key',
                                'revoke_key'   => 'fa-times-circle',
                            ];
                            $color = $colors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            $icon  = $icons[$log->action]  ?? 'fa-circle';
                        @endphp
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs {{ $color }}">
                            <i class="fas {{ $icon }}"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium">{{ str_replace('_', ' ', $log->action) }}</p>
                            <p class="text-xs text-gray-400">{{ $log->application->name ?? 'Dashboard' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $log->status }}
                        </span>
                        <p class="text-xs text-gray-400 mt-1">{{ $log->performed_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Aucune activité.</p>
            @endforelse
        </div>

    </div>

    <!-- DOCUMENTATION RAPIDE -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">📖 Intégration rapide</h3>
        <div class="flex gap-2 mb-4" id="docTabs">
            <button onclick="showDoc('php')" class="doc-tab active px-4 py-2 rounded-xl text-sm font-medium bg-emerald-600 text-white">PHP</button>
            <button onclick="showDoc('js')" class="doc-tab px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-600">JavaScript</button>
            <button onclick="showDoc('java')" class="doc-tab px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-600">Java</button>
            <button onclick="showDoc('python')" class="doc-tab px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-600">Python</button>
        </div>

        <div id="doc-php" class="doc-content">
            <pre class="bg-gray-900 text-green-400 p-4 rounded-xl text-xs overflow-auto"><code>// Chiffrement avec CryptoVault API (PHP)
$apiKey = "VOTRE_CLÉ_API";

// 1. Chiffrer une donnée
$ch = curl_init("{{ url('/api/v1/encrypt') }}");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode(["data" => "4532123456789123"]),
    CURLOPT_RETURNTRANSFER => true,
]);
$response = json_decode(curl_exec($ch));

// Stocker dans votre base de données
$encrypted     = $response->encrypted;
$encrypted_dek = $response->encrypted_dek;
$version       = $response->version;

// 2. Déchiffrer
$ch = curl_init("{{ url('/api/v1/decrypt') }}");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "encrypted"     => $encrypted,
        "encrypted_dek" => $encrypted_dek,
        "version"       => $version
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
$result = json_decode(curl_exec($ch));
echo $result->decrypted; // 4532123456789123</code></pre>
        </div>

        <div id="doc-js" class="doc-content hidden">
            <pre class="bg-gray-900 text-green-400 p-4 rounded-xl text-xs overflow-auto"><code>// Chiffrement avec CryptoVault API (JavaScript)
const API_KEY = "VOTRE_CLÉ_API";
const BASE_URL = "{{ url('/api/v1') }}";

// 1. Chiffrer
const encryptResponse = await fetch(`${BASE_URL}/encrypt`, {
    method: "POST",
    headers: {
        "Authorization": `Bearer ${API_KEY}`,
        "Content-Type": "application/json"
    },
    body: JSON.stringify({ data: "4532123456789123" })
});
const { encrypted, encrypted_dek, version } = await encryptResponse.json();

// Stocker encrypted, encrypted_dek, version dans votre DB

// 2. Déchiffrer
const decryptResponse = await fetch(`${BASE_URL}/decrypt`, {
    method: "POST",
    headers: {
        "Authorization": `Bearer ${API_KEY}`,
        "Content-Type": "application/json"
    },
    body: JSON.stringify({ encrypted, encrypted_dek, version })
});
const { decrypted } = await decryptResponse.json();
console.log(decrypted); // 4532123456789123</code></pre>
        </div>

        <div id="doc-java" class="doc-content hidden">
            <pre class="bg-gray-900 text-green-400 p-4 rounded-xl text-xs overflow-auto"><code>// Chiffrement avec CryptoVault API (Java)
import java.net.http.*;
import java.net.URI;

String apiKey = "VOTRE_CLÉ_API";
HttpClient client = HttpClient.newHttpClient();

// 1. Chiffrer
HttpRequest encryptRequest = HttpRequest.newBuilder()
    .uri(URI.create("{{ url('/api/v1/encrypt') }}"))
    .header("Authorization", "Bearer " + apiKey)
    .header("Content-Type", "application/json")
    .POST(HttpRequest.BodyPublishers.ofString(
        "{\"data\": \"4532123456789123\"}"
    ))
    .build();

HttpResponse&lt;String&gt; response = client.send(
    encryptRequest,
    HttpResponse.BodyHandlers.ofString()
);

// Parser le JSON response pour récupérer encrypted, encrypted_dek, version
// Stocker dans votre base de données

// 2. Déchiffrer avec les mêmes valeurs
HttpRequest decryptRequest = HttpRequest.newBuilder()
    .uri(URI.create("{{ url('/api/v1/decrypt') }}"))
    .header("Authorization", "Bearer " + apiKey)
    .header("Content-Type", "application/json")
    .POST(HttpRequest.BodyPublishers.ofString(
        "{\"encrypted\":\"...\",\"encrypted_dek\":\"...\",\"version\":1}"
    ))
    .build();</code></pre>
        </div>

        <div id="doc-python" class="doc-content hidden">
            <pre class="bg-gray-900 text-green-400 p-4 rounded-xl text-xs overflow-auto"><code># Chiffrement avec CryptoVault API (Python)
import requests

API_KEY = "VOTRE_CLÉ_API"
BASE_URL = "{{ url('/api/v1') }}"
HEADERS = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json"
}

# 1. Chiffrer
response = requests.post(f"{BASE_URL}/encrypt",
    headers=HEADERS,
    json={"data": "4532123456789123"}
)
data = response.json()
encrypted     = data["encrypted"]
encrypted_dek = data["encrypted_dek"]
version       = data["version"]

# Stocker dans votre base de données

# 2. Déchiffrer
result = requests.post(f"{BASE_URL}/decrypt",
    headers=HEADERS,
    json={
        "encrypted": encrypted,
        "encrypted_dek": encrypted_dek,
        "version": version
    }
)
print(result.json()["decrypted"])  # 4532123456789123</code></pre>
        </div>
    </div>

</div>

<script>
// Testeur API
let encryptedData = null;

async function testEncrypt() {
    const data    = document.getElementById('testData').value;
    const apiKey  = document.getElementById('testKey').value;
    const result  = document.getElementById('testResult');

    if (!apiKey) { alert('Sélectionnez une clé API'); return; }

    result.textContent = '⏳ Chiffrement en cours...';

    try {
        const res = await fetch('/api/v1/encrypt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + apiKey
            },
            body: JSON.stringify({ data })
        });
        const json = await res.json();
        if (json.success) {
            encryptedData = json;
            result.textContent = JSON.stringify(json, null, 2);
            document.getElementById('decryptBtn').disabled = false;
        } else {
            result.textContent = 'Erreur: ' + JSON.stringify(json);
        }
    } catch(e) {
        result.textContent = 'Erreur: ' + e.message;
    }
}

async function testDecrypt() {
    if (!encryptedData) return;
    const apiKey = document.getElementById('testKey').value;
    const result = document.getElementById('testResult');

    result.textContent = '⏳ Déchiffrement en cours...';

    try {
        const res = await fetch('/api/v1/decrypt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + apiKey
            },
            body: JSON.stringify({
                encrypted:     encryptedData.encrypted,
                encrypted_dek: encryptedData.encrypted_dek,
                version:       encryptedData.version
            })
        });
        const json = await res.json();
        result.textContent = JSON.stringify(json, null, 2);
    } catch(e) {
        result.textContent = 'Erreur: ' + e.message;
    }
}

// Clés API
const keyVisible = {};
function toggleKey(id, fullKey) {
    const el = document.getElementById('key-' + id);
    if (keyVisible[id]) {
        el.textContent = fullKey.substring(0, 16) + '...';
        keyVisible[id] = false;
    } else {
        el.textContent = fullKey;
        keyVisible[id] = true;
    }
}
function copyKey(key, id) {
    navigator.clipboard.writeText(key);
    const el = document.getElementById('key-' + id);
    const original = el.textContent;
    el.textContent = '✅ Copié !';
    setTimeout(() => { el.textContent = original; }, 1500);
}

// Tabs documentation
function showDoc(lang) {
    document.querySelectorAll('.doc-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.doc-tab').forEach(el => {
        el.classList.remove('bg-emerald-600', 'text-white');
        el.classList.add('bg-gray-100', 'text-gray-600');
    });
    document.getElementById('doc-' + lang).classList.remove('hidden');
    event.target.classList.remove('bg-gray-100', 'text-gray-600');
    event.target.classList.add('bg-emerald-600', 'text-white');
}
</script>
@endsection