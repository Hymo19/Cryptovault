@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Applications</h2>
            <p class="text-gray-500 mt-1">Gérez vos applications connectées</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nouvelle application
        </button>
    </div>

    @if($plan)
        <div class="bg-white rounded-2xl shadow p-4 mb-6 flex items-center gap-4">
            <div class="bg-orange-100 p-3 rounded-xl">
                <i class="fas fa-tags text-orange-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Plan <span class="text-orange-600">{{ $plan->name }}</span></p>
                <p class="text-xs text-gray-400">{{ $applications->total() }} / {{ $plan->max_apps }} applications utilisées</p>
            </div>
            <div class="ml-auto w-48 bg-gray-200 rounded-full h-2">
                <div class="bg-orange-500 h-2 rounded-full"
                     style="width: {{ min(100, ($applications->total() / $plan->max_apps) * 100) }}%"></div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b bg-gray-50">
                <tr class="text-gray-500">
                    <th class="px-6 py-4 text-left">Application</th>
                    <th class="px-6 py-4 text-left">Chiffrements</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Créée le</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $app->name }}</p>
                        <p class="text-xs text-gray-400">{{ $app->description }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ number_format($app->total_encryptions) }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full
                            {{ $app->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $app->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $app->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 flex gap-2">
                        @if($app->status === 'active')
                            <form method="POST" action="{{ route('applications.suspend', $app) }}">
                                @csrf
                                <button class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg transition">
                                    Suspendre
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('applications.activate', $app) }}">
                                @csrf
                                <button class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-1 rounded-lg transition">
                                    Activer
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('applications.destroy', $app) }}"
                              onsubmit="return confirm('Supprimer cette application ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        Aucune application. Créez-en une pour commencer.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $applications->links() }}</div>
    </div>
</div>

<!-- MODAL CREATION -->
<div id="modal-create" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Nouvelle application</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('applications.store') }}">
            @csrf
            <div class="mb-4">
                <label class="text-sm text-gray-600 font-medium">Nom de l'application</label>
                <input type="text" name="name" required placeholder="Mon App"
                       class="w-full mt-1 px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="mb-6">
                <label class="text-sm text-gray-600 font-medium">Description (optionnel)</label>
                <input type="text" name="description" placeholder="Description courte"
                       class="w-full mt-1 px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                Créer l'application
            </button>
        </form>
    </div>
</div>
@endsection