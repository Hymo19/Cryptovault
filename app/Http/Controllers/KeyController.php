<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\ApiKey;
use App\Models\ActivityLog;

class KeyController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $apiKeys = ApiKey::whereHas('application', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->with('application')->latest()->paginate(15);

        $applications = $tenant->applications()->where('status', 'active')->get();

        return view('keys.index', compact('apiKeys', 'applications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $tenant = Auth::user()->tenant;
        $app    = $tenant->applications()->findOrFail($request->application_id);

        $apiKey = $app->apiKeys()->create([
            'key'       => 'cvk_' . Str::random(40),
            'status'    => 'active',
            'tenant_id' => $tenant->id,
        ]);

        ActivityLog::create([
            'tenant_id'      => $tenant->id,
            'application_id' => $app->id,
            'action'         => 'generate_key',
            'ip_address'     => $request->ip(),
            'status'         => 'success',
            'message'        => 'Clé API générée pour "' . $app->name . '".',
            'performed_at'   => now(),
        ]);

        return back()->with('success', 'Clé API générée : ' . $apiKey->key);
    }

    public function revoke(ApiKey $apiKey)
    {
        $tenant = Auth::user()->tenant;
        $app    = $tenant->applications()->find($apiKey->application_id);

        if (!$app) abort(403);

        $apiKey->update(['status' => 'revoked']);

        ActivityLog::create([
            'tenant_id'      => $tenant->id,
            'application_id' => $app->id,
            'action'         => 'revoke_key',
            'ip_address'     => request()->ip(),
            'status'         => 'success',
            'message'        => 'Clé API révoquée pour "' . $app->name . '".',
            'performed_at'   => now(),
        ]);

        return back()->with('success', 'Clé API révoquée.');
    }
}