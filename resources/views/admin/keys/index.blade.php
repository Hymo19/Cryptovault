@extends('layouts.admin')
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold">Master Keys</h2>
            <p class="text-gray-400 mt-1">Gestion des clés de chiffrement</p>
        </div>
        <form method="POST" action="{{ route('admin.keys.rotate') }}"
              onsubmit="return confirm('Confirmer la rotation ? Toutes les données seront re-chiffrées.')">
            @csrf
            <button class="bg-orange-600 hover:bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Lancer une rotation
            </button>
        </form>
    </div>

    <!-- Statut en temps réel -->
    <div class="bg-gray-900 rounded-2xl p-6 border border-gray-800 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold">Statut de rotation</h3>
            <button onclick="loadStatus()" class="text-sm text-gray-400 hover:text-white transition">
                <i class="fas fa-refresh mr-1"></i> Actualiser
            </button>
        </div>
        <div id="status-box" class="text-gray-400 text-sm">
            Cliquez sur Actualiser pour voir le statut...
        </div>
    </div>

    <!-- Liste des clés -->
    <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-800">
                <tr class="text-gray-400">
                    <th class="px-6 py-4 text-left">Version</th>
                    <th class="px-6 py-4 text-left">Tenant</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Créée le</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keys as $key)
                <tr class="border-b border-gray-800 last:border-0 hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-white">v{{ $key->id }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $key->tenant->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @if($key->is_active)
                            <span class="bg-emerald-900 text-emerald-400 px-3 py-1 rounded-full text-xs">✅ Active</span>
                        @else
                            <span class="bg-gray-800 text-gray-500 px-3 py-1 rounded-full text-xs">Archivée</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $key->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune clé.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $keys->links() }}</div>
    </div>
</div>

<script>
async function loadStatus() {
    const box = document.getElementById('status-box');
    box.textContent = 'Chargement...';
    try {
        const res  = await fetch('{{ route("admin.keys.status") }}');
        const json = await res.json();
        if (json.success) {
            const d = json.data;
            box.innerHTML = `
                <div class="grid grid-cols-3 gap-4">
                    <div><p class="text-xs text-gray-500">Version active</p><p class="text-white font-bold text-lg">v${d.current_version}</p></div>
                    <div><p class="text-xs text-gray-500">À jour</p><p class="text-emerald-400 font-bold text-lg">${d.up_to_date}</p></div>
                    <div><p class="text-xs text-gray-500">Orphelins</p><p class="text-red-400 font-bold text-lg">${d.orphan_cards ?? 0}</p></div>
                </div>`;
        } else {
            box.textContent = 'Erreur : ' + json.message;
        }
    } catch(e) {
        box.textContent = 'Erreur de connexion.';
    }
}
</script>
@endsection