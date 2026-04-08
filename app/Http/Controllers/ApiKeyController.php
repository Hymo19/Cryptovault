<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function generate(Application $application)
    {
        abort_if($application->tenant_id !== Auth::user()->tenant_id, 403);

        ApiKey::create([
            'application_id' => $application->id,
            'tenant_id'      => Auth::user()->tenant_id,
            'key'            => 'cv_' . Str::random(60),
            'status'         => 'active',
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'Nouvelle clé API générée.');
    }

    public function revoke(ApiKey $apiKey)
    {
        abort_if($apiKey->tenant_id !== Auth::user()->tenant_id, 403);

        $apiKey->update(['status' => 'revoked']);

        return redirect()->route('applications.show', $apiKey->application)
            ->with('success', 'Clé API révoquée.');
    }
}