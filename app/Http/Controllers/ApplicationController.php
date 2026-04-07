<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Auth::user()->tenant->applications()->latest()->get();
        return view('applications.index', compact('applications'));
    }

    public function create()
    {
        return view('applications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $application = Application::create([
            'tenant_id'   => Auth::user()->tenant_id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => 'active',
        ]);

        // Générer une clé API automatiquement
        ApiKey::create([
            'application_id' => $application->id,
            'tenant_id'      => Auth::user()->tenant_id,
            'key'            => 'cv_' . Str::random(60),
            'status'         => 'active',
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application créée avec succès.');
    }

    public function show(Application $application)
    {
        // Sécurité : vérifier que l'app appartient au tenant
        abort_if($application->tenant_id !== Auth::user()->tenant_id, 403);

        $apiKeys = $application->apiKeys()->latest()->get();
        return view('applications.show', compact('application', 'apiKeys'));
    }

    public function destroy(Application $application)
    {
        abort_if($application->tenant_id !== Auth::user()->tenant_id, 403);

        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Application supprimée.');
    }
}