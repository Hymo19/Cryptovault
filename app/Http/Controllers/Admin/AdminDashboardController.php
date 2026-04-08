<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\ActivityLog;
use App\Models\MasterKey;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'      => Tenant::where('email', '!=', 'admin@cryptovault.com')->count(),
            'total_encryptions'  => ActivityLog::where('action', 'encrypt')->count(),
            'total_decryptions'  => ActivityLog::where('action', 'decrypt')->count(),
            'total_rotations'    => ActivityLog::where('action', 'rotate')->count(),
            'active_key'         => MasterKey::where('is_active', true)->latest()->first(),
            'total_keys'         => MasterKey::count(),
            'recent_logs'        => ActivityLog::with(['tenant', 'application'])
                                               ->latest('performed_at')
                                               ->take(10)
                                               ->get(),
            'tenants'            => Tenant::where('email', '!=', 'admin@cryptovault.com')
                                          ->withCount('applications')
                                          ->latest()
                                          ->take(5)
                                          ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}