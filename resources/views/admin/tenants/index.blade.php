@extends('layouts.admin')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Tenants</h2>
        <p class="text-gray-400 mt-1">Toutes les entreprises clientes</p>
    </div>

    <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-800">
                <tr class="text-gray-400">
                    <th class="px-6 py-4 text-left">Entreprise</th>
                    <th class="px-6 py-4 text-left">Plan</th>
                    <th class="px-6 py-4 text-left">Apps</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Inscrit le</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr class="border-b border-gray-800 last:border-0 hover:bg-gray-800 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-white">{{ $tenant->name }}</p>
                        <p class="text-xs text-gray-500">{{ $tenant->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-300">
                        {{ $tenant->subscription->plan->name ?? 'Free' }}
                    </td>
                    <td class="px-6 py-4 text-gray-300">{{ $tenant->applications_count }}</td>
                    <td class="px-6 py-4">
                        @if($tenant->status === 'active')
                            <span class="bg-emerald-900 text-emerald-400 px-2 py-1 rounded-full text-xs">Actif</span>
                        @elseif($tenant->status === 'trial')
                            <span class="bg-blue-900 text-blue-400 px-2 py-1 rounded-full text-xs">Essai</span>
                        @else
                            <span class="bg-red-900 text-red-400 px-2 py-1 rounded-full text-xs">Suspendu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $tenant->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('admin.tenants.show', $tenant) }}"
                           class="text-xs bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded-lg transition">
                            Voir
                        </a>
                        @if($tenant->status !== 'suspended')
                        <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}">
                            @csrf
                            <button class="text-xs bg-red-900 hover:bg-red-800 text-red-300 px-3 py-1 rounded-lg transition">
                                Suspendre
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}">
                            @csrf
                            <button class="text-xs bg-emerald-900 hover:bg-emerald-800 text-emerald-300 px-3 py-1 rounded-lg transition">
                                Activer
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun tenant.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $tenants->links() }}</div>
    </div>
</div>
@endsection