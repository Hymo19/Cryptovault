@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Clés API</h2>
            <p class="text-gray-500 mt-1">Gérez vos clés d'accès à l'API</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Générer une clé
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b bg-gray-50">
                <tr class="text-gray-500">
                    <th class="px-6 py-4 text-left">Clé</th>
                    <th class="px-6 py-4 text-left">Application</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Créée le</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($apiKeys as $apiKey)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono text-xs text-gray-600">
                        <span id="key-{{ $apiKey->id }}">{{ Str::limit($apiKey->key, 20) }}...</span>
                        <button onclick="toggleKey({{ $apiKey->id }}, '{{ $apiKey->key }}')"
                                class="ml-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button onclick="copyKey('{{ $apiKey->key }}')"
                                class="ml-1 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-copy text-xs"></i>
                        </button>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $apiKey->application->name }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full
                            {{ $apiKey->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $apiKey->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $apiKey->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($apiKey->status === 'active')
                            <form method="POST" action="{{ route('keys.revoke', $apiKey) }}"
                                  onsubmit="return confirm('Révoquer cette clé ?')">
                                @csrf
                                <button class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg transition">
                                    Révoquer
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">Révoquée</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">Aucune clé API.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $apiKeys->links() }}</div>
    </div>
</div>

<!-- MODAL -->
<div id="modal-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Générer une clé API</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('keys.store') }}">
            @csrf
            <div class="mb-6">
                <label class="text-sm text-gray-600 font-medium">Application</label>
                <select name="application_id" required
                        class="w-full mt-1 px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Sélectionner une application</option>
                    @foreach($applications as $app)
                        <option value="{{ $app->id }}">{{ $app->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                Générer
            </button>
        </form>
    </div>
</div>

<script>
const keyValues = {};
function toggleKey(id, fullKey) {
    const el = document.getElementById('key-' + id);
    if (keyValues[id]) {
        el.textContent = fullKey.substring(0, 20) + '...';
        keyValues[id] = false;
    } else {
        el.textContent = fullKey;
        keyValues[id] = true;
    }
}
function copyKey(key) {
    navigator.clipboard.writeText(key);
    alert('Clé copiée !');
}
</script>
@endsection