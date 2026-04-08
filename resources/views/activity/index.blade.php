@extends('layouts.app')

@section('title', 'Activité')
@section('page-title', 'Journal d\'activité')

@section('content')

{{-- Filtres --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 mb-6">
    <form method="GET" action="{{ route('activity.index') }}" class="flex items-center gap-4 flex-wrap">
        
        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-400">Action</label>
            <select name="action"
                class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                <option value="">Toutes</option>
                <option value="encrypt"   {{ request('action') === 'encrypt'    ? 'selected' : '' }}>Chiffrement</option>
                <option value="decrypt"   {{ request('action') === 'decrypt'    ? 'selected' : '' }}>Déchiffrement</option>
                <option value="rotate"    {{ request('action') === 'rotate'     ? 'selected' : '' }}>Rotation</option>
                <option value="login"     {{ request('action') === 'login'      ? 'selected' : '' }}>Connexion</option>
                <option value="logout"    {{ request('action') === 'logout'     ? 'selected' : '' }}>Déconnexion</option>
                <option value="create_app"{{ request('action') === 'create_app' ? 'selected' : '' }}>Création app</option>
                <option value="revoke_key"{{ request('action') === 'revoke_key' ? 'selected' : '' }}>Révocation clé</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-400">Statut</label>
            <select name="status"
                class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                <option value="">Tous</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Succès</option>
                <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Échec</option>
            </select>
        </div>

        <button type="submit"
            class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
            Filtrer
        </button>

        @if(request('action') || request('status'))
        <a href="{{ route('activity.index') }}"
            class="text-xs text-gray-400 hover:text-white transition">
            Réinitialiser
        </a>
        @endif

        <span class="ml-auto text-xs text-gray-500">{{ $logs->total() }} entrée(s)</span>
    </form>
</div>

{{-- Table --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400">Action</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400">Application</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400">Statut</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400">IP</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-800/50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        @php
                            $icons = [
                                'encrypt'    => ['icon' => 'fa-lock',             'color' => 'text-indigo-400'],
                                'decrypt'    => ['icon' => 'fa-lock-open',        'color' => 'text-blue-400'],
                                'rotate'     => ['icon' => 'fa-rotate',           'color' => 'text-yellow-400'],
                                'login'      => ['icon' => 'fa-right-to-bracket', 'color' => 'text-green-400'],
                                'logout'     => ['icon' => 'fa-right-from-bracket','color' => 'text-gray-400'],
                                'create_app' => ['icon' => 'fa-cube',             'color' => 'text-purple-400'],
                                'revoke_key' => ['icon' => 'fa-ban',              'color' => 'text-red-400'],
                                'init'       => ['icon' => 'fa-shield-halved',    'color' => 'text-teal-400'],
                            ];
                            $icon = $icons[$log->action] ?? ['icon' => 'fa-circle', 'color' => 'text-gray-400'];
                        @endphp
                        <i class="fa-solid {{ $icon['icon'] }} {{ $icon['color'] }} text-xs w-4"></i>
                        <span class="text-white capitalize">{{ str_replace('_', ' ', $log->action) }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs">
                    {{ $log->application?->name ?? '—' }}
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $log->status === 'success' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                        {{ $log->status === 'success' ? 'Succès' : 'Échec' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs font-mono">
                    {{ $log->ip_address ?? '—' }}
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs">
                    {{ $log->performed_at->format('d/m/Y H:i:s') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center">
                    <i class="fa-solid fa-list-check text-gray-600 text-2xl mb-3 block"></i>
                    <p class="text-sm text-gray-500">Aucune activité enregistrée</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-800">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection