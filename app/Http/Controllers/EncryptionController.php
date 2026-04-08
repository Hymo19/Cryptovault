<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\ActivityLog;
use App\Services\EnvelopeEncryptionService;
use Illuminate\Http\Request;

class EncryptionController extends Controller
{
    protected EnvelopeEncryptionService $service;

    public function __construct(EnvelopeEncryptionService $service)
    {
        $this->service = $service;
    }

    /**
     * POST /api/encrypt
     */
    public function encrypt(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
        ]);

        $apiKey = $this->resolveApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API invalide ou révoquée.'], 401);
        }

        $tenant = $apiKey->tenant;
        $application = $apiKey->application;

        try {
            $result = $this->service->encrypt($tenant, $request->data);

            // Mettre à jour les stats
            $application->increment('total_encryptions');
            $application->update(['last_used_at' => now()]);
            $apiKey->update(['last_used_at' => now()]);

            // Logger
            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $application->id,
                'action'         => 'encrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'success',
                'performed_at'   => now(),
            ]);

            return response()->json([
                'success' => true,
                'payload' => $result,
            ]);

        } catch (\Exception $e) {
            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $application->id,
                'action'         => 'encrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'failed',
                'message'        => $e->getMessage(),
                'performed_at'   => now(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/decrypt
     */
    public function decrypt(Request $request)
    {
        $request->validate([
            'payload'                => 'required|array',
            'payload.ciphertext'    => 'required|string',
            'payload.iv'            => 'required|string',
            'payload.tag'           => 'required|string',
            'payload.encrypted_dek' => 'required|string',
            'payload.dek_iv'        => 'required|string',
            'payload.dek_tag'       => 'required|string',
            'payload.master_key_id' => 'required|integer',
        ]);

        $apiKey = $this->resolveApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API invalide ou révoquée.'], 401);
        }

        $tenant = $apiKey->tenant;
        $application = $apiKey->application;

        try {
            $plaintext = $this->service->decrypt($tenant, $request->payload);

            $application->increment('total_decryptions');
            $application->update(['last_used_at' => now()]);
            $apiKey->update(['last_used_at' => now()]);

            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $application->id,
                'action'         => 'decrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'success',
                'performed_at'   => now(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $plaintext,
            ]);

        } catch (\Exception $e) {
            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $application->id,
                'action'         => 'decrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'failed',
                'message'        => $e->getMessage(),
                'performed_at'   => now(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Résoudre la clé API depuis le header
     */
    private function resolveApiKey(Request $request): ?ApiKey
    {
        $key = $request->header('X-API-KEY');
        if (!$key) return null;

        return ApiKey::where('key', $key)
            ->where('status', 'active')
            ->with(['tenant', 'application'])
            ->first();
    }
}