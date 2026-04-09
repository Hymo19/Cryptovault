<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\ActivityLog;

class ApplicationController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $applications = $tenant->applications()->latest()->paginate(10);
        $plan = $tenant->subscription->plan ?? null;

        return view('applications.index', compact('applications', 'plan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $tenant = Auth::user()->tenant;
        $plan   = $tenant->subscription->plan ?? null;

        if ($plan && $tenant->applications()->count() >= $plan->max_apps) {
            return back()->with('error', 'Limite d\'applications atteinte pour votre plan.');
        }

        $app = $tenant->applications()->create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => 'active',
        ]);

        $app->apiKeys()->create([
            'key'       => 'cvk_' . Str::random(40),
            'status'    => 'active',
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        ActivityLog::create([
            'tenant_id'      => Auth::user()->tenant_id,
            'application_id' => $app->id,
            'action'         => 'create_app',
            'ip_address'     => $request->ip(),
            'status'         => 'success',
            'message'        => 'Application "' . $app->name . '" créée.',
            'performed_at'   => now(),
        ]);

        return back()->with('success', 'Application "' . $app->name . '" créée avec succès.');
    }

    public function destroy(Application $application)
    {
        if ($application->tenant_id !== Auth::user()->tenant_id) abort(403);

        ActivityLog::create([
            'tenant_id'      => Auth::user()->tenant_id,
            'application_id' => $application->id,
            'action'         => 'delete_app',
            'ip_address'     => request()->ip(),
            'status'         => 'success',
            'message'        => 'Application "' . $application->name . '" supprimée.',
            'performed_at'   => now(),
        ]);

        $application->delete();
        return back()->with('success', 'Application supprimée.');
    }

    public function suspend(Application $application)
    {
        if ($application->tenant_id !== Auth::user()->tenant_id) abort(403);

        $application->update(['status' => 'suspended']);

        ActivityLog::create([
            'tenant_id'      => Auth::user()->tenant_id,
            'application_id' => $application->id,
            'action'         => 'suspend_app',
            'ip_address'     => request()->ip(),
            'status'         => 'success',
            'message'        => 'Application "' . $application->name . '" suspendue.',
            'performed_at'   => now(),
        ]);

        return back()->with('success', 'Application suspendue.');
    }

    public function activate(Application $application)
    {
        if ($application->tenant_id !== Auth::user()->tenant_id) abort(403);

        $application->update(['status' => 'active']);

        ActivityLog::create([
            'tenant_id'      => Auth::user()->tenant_id,
            'application_id' => $application->id,
            'action'         => 'activate_app',
            'ip_address'     => request()->ip(),
            'status'         => 'success',
            'message'        => 'Application "' . $application->name . '" activée.',
            'performed_at'   => now(),
        ]);

        return back()->with('success', 'Application activée.');
    }
}