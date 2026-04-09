<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ApiKey;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $plan   = $tenant->subscription->plan ?? null;

        $totalOps      = $tenant->activityLogs()
                                ->whereIn('action', ['encrypt', 'decrypt'])
                                ->whereMonth('performed_at', now()->month)
                                ->count();

        $quotaPercent  = $plan ? round(($totalOps / $plan->max_ops_per_month) * 100) : 0;

        $apiKeys = ApiKey::whereHas('application', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->with('application')
          ->where('status', 'active')
          ->latest()
          ->take(5)
          ->get();

        $stats = [
            'total_apps'        => $tenant->applications()->count(),
            'total_encryptions' => $tenant->activityLogs()->where('action', 'encrypt')->whereMonth('performed_at', now()->month)->count(),
            'total_decryptions' => $tenant->activityLogs()->where('action', 'decrypt')->whereMonth('performed_at', now()->month)->count(),
            'total_ops'         => $totalOps,
            'quota_percent'     => $quotaPercent,
            'recent_apps'       => $tenant->applications()->latest()->take(5)->get(),
            'api_keys'          => $apiKeys,
            'recent_logs'       => $tenant->activityLogs()
                                          ->with('application')
                                          ->latest('performed_at')
                                          ->take(8)
                                          ->get(),
        ];

        return view('dashboard', compact('stats', 'tenant', 'plan'));
    }
}