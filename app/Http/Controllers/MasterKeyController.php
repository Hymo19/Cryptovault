<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\EnvelopeEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterKeyController extends Controller
{
    protected EnvelopeEncryptionService $service;

    public function __construct(EnvelopeEncryptionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tenant = Auth::user()->tenant;
        $masterKeys = $tenant->masterKeys()->latest()->get();
        $activeMasterKey = $tenant->activeMasterKey;

        return view('masterkeys.index', compact('masterKeys', 'activeMasterKey'));
    }

    public function rotate(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $this->service->rotateMasterKey($tenant);

        ActivityLog::create([
            'tenant_id'    => $tenant->id,
            'action'       => 'rotate',
            'ip_address'   => $request->ip(),
            'status'       => 'success',
            'message'      => 'Rotation de la master key effectuée.',
            'performed_at' => now(),
        ]);

        return redirect()->route('masterkeys.index')
            ->with('success', 'Master Key tournée avec succès.');
    }
}