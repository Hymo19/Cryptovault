<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        $logs = $tenant->activityLogs()
                       ->with('application')
                       ->latest('performed_at')
                       ->paginate(20);

        return view('logs.index', compact('logs'));
    }
}