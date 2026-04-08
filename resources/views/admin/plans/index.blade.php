@extends('layouts.admin')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Plans d'abonnement</h2>
        <p class="text-gray-400 mt-1">Configure les limites par plan</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6">
            <h3 class="text-xl font-bold mb-1 {{ $plan->name === 'Enterprise' ? 'text-yellow-400' : ($plan->name === 'Pro' ? 'text-violet-400' : 'text-gray-300') }}">
                {{ $plan->name }}
            </h3>
            <p class="text-sm text-gray-500 mb-4">{{ $plan->subscriptions_count }} abonné(s)</p>

            <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="text-xs text-gray-400">Apps maximum</label>
                    <input type="number" name="max_apps" value="{{ $plan->max_apps }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 mt-1 text-white text-sm focus:outline-none focus:border-gray-500">
                </div>
                <div class="mb-3">
                    <label class="text-xs text-gray-400">Opérations / mois</label>
                    <input type="number" name="max_ops_per_month" value="{{ $plan->max_ops_per_month }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 mt-1 text-white text-sm focus:outline-none focus:border-gray-500">
                </div>
                <div class="mb-4">
                    <label class="text-xs text-gray-400">Prix mensuel (€)</label>
                    <input type="number" step="0.01" name="price" value="{{ $plan->price }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 mt-1 text-white text-sm focus:outline-none focus:border-gray-500">
                </div>

                <button class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-xl text-sm font-semibold transition">
                    Enregistrer
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection