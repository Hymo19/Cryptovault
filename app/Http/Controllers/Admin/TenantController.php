<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['applications', 'activityLogs'])
                         ->with('subscription.plan')
                         ->latest()
                         ->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['applications.apiKeys', 'masterKeys', 'subscription.plan']);

        $stats = [
            'total_apps'        => $tenant->applications()->count(),
            'total_encryptions' => $tenant->activityLogs()->where('action', 'encrypt')->count(),
            'total_decryptions' => $tenant->activityLogs()->where('action', 'decrypt')->count(),
            'recent_logs'       => $tenant->activityLogs()
                                          ->with('application')
                                          ->latest('performed_at')
                                          ->take(10)
                                          ->get(),
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);
        return back()->with('success', 'Tenant suspendu.');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        return back()->with('success', 'Tenant activé.');
    }
}