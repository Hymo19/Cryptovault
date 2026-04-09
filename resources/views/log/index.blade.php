@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Logs d'activité</h2>
        <p class="text-gray-500 mt-1">Historique complet de vos opérations</p>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b bg-gray-50">
                <tr class="text-gray-500">
                    <th class="px-6 py-4 text-left">Action</th>
                    <th class="px-6 py-4 text-left">Message</th>
                    <th class="px-6 py-4 text-left">Application</th>
                    <th class="px-6 py-4 text-left">IP</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'encrypt'      => 'bg-emerald-100 text-emerald-700',
                                'decrypt'      => 'bg-violet-100 text-violet-700',
                                'login'        => 'bg-blue-100 text-blue-700',
                                'rotate'       => 'bg-orange-100 text-orange-700',
                                'revoke'       => 'bg-red-100 text-red-700',
                                'create_app'   => 'bg-teal-100 text-teal-700',
                                'delete_app'   => 'bg-red-100 text-red-700',
                                'suspend_app'  => 'bg-yellow-100 text-yellow-700',
                                'activate_app' => 'bg-green-100 text-green-700',
                                'generate_key' => 'bg-indigo-100 text-indigo-700',
                                'revoke_key'   => 'bg-pink-100 text-pink-700',
                            ];
                            $icons = [
                                'encrypt'      => 'fa-lock',
                                'decrypt'      => 'fa-lock-open',
                                'login'        => 'fa-sign-in-alt',
                                'rotate'       => 'fa-sync-alt',
                                'revoke'       => 'fa-ban',
                                'create_app'   => 'fa-plus-circle',
                                'delete_app'   => 'fa-trash',
                                'suspend_app'  => 'fa-pause-circle',
                                'activate_app' => 'fa-check-circle',
                                'generate_key' => 'fa-key',
                                'revoke_key'   => 'fa-times-circle',
                            ];
                            $color = $colors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            $icon  = $icons[$log->action]  ?? 'fa-circle';
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs {{ $color }}">
                                <i class="fas {{ $icon }}"></i>
                            </span>
                            <span class="font-medium text-gray-700">{{ str_replace('_', ' ', $log->action) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $log->message ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $log->application->name ?? '—' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full
                            {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">
                        {{ $log->performed_at->format('d/m/Y H:i:s') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucun log.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection