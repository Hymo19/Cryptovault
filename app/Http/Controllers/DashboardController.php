<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        // Statistiques pour le dashboard
        $stats = [
            'total_apps'         => $tenant->applications()->count(),
            'total_encryptions'  => $tenant->applications()->sum('total_encryptions'),
            'total_decryptions'  => $tenant->applications()->sum('total_decryptions'),
            'active_key_version' => $tenant->masterKeys()
                                           ->where('is_active', true)
                                           ->value('id') ?? 'Non initialisé',
            'recent_logs'        => $tenant->activityLogs()
                                           ->latest('performed_at')
                                           ->take(5)
                                           ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}