<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiKey;
use App\Models\ActivityLog;

class CryptoController extends Controller
{
    private function resolveApiKey(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) return null;

        return ApiKey::with('application.tenant')
                     ->where('key', $bearer)
                     ->where('status', 'active')
                     ->first();
    }

    public function encrypt(Request $request)
    {
        $apiKey = $this->resolveApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API invalide ou révoquée.'], 401);
        }

        $request->validate(['data' => 'required|string']);

        $app    = $apiKey->application;
        $tenant = $app->tenant;

        if ($app->status !== 'active') {
            return response()->json(['error' => 'Application suspendue.'], 403);
        }

        try {
            $result = DB::selectOne("SELECT * FROM encrypt_card_secure(?)", [$request->data]);

            $app->increment('total_encryptions');

            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $app->id,
                'action'         => 'encrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'success',
                'message'        => 'Chiffrement via API.',
                'performed_at'   => now(),
            ]);

            return response()->json([
                'success'       => true,
                'encrypted'     => base64_encode(stream_get_contents($result->encrypted)),
                'encrypted_dek' => base64_encode(stream_get_contents($result->encrypted_dek)),
                'version'       => $result->version,
            ]);

        } catch (\Exception $e) {
            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $app->id,
                'action'         => 'encrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'failed',
                'message'        => $e->getMessage(),
                'performed_at'   => now(),
            ]);

            return response()->json(['error' => 'Erreur de chiffrement : ' . $e->getMessage()], 500);
        }
    }

    public function decrypt(Request $request)
    {
        $apiKey = $this->resolveApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API invalide ou révoquée.'], 401);
        }

        $request->validate([
            'encrypted'     => 'required|string',
            'encrypted_dek' => 'required|string',
            'version'       => 'required|integer',
        ]);

        $app    = $apiKey->application;
        $tenant = $app->tenant;

        if ($app->status !== 'active') {
            return response()->json(['error' => 'Application suspendue.'], 403);
        }

        try {
            $encrypted     = bin2hex(base64_decode($request->encrypted));
            $encrypted_dek = bin2hex(base64_decode($request->encrypted_dek));

            $result = DB::selectOne("SELECT decrypt_card_secure(decode(?, 'hex'), decode(?, 'hex'), ?) AS decrypted", [
                $encrypted,
                $encrypted_dek,
                $request->version,
            ]);

            $app->increment('total_decryptions');

            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $app->id,
                'action'         => 'decrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'success',
                'message'        => 'Déchiffrement via API.',
                'performed_at'   => now(),
            ]);

            return response()->json([
                'success'   => true,
                'decrypted' => $result->decrypted,
            ]);

        } catch (\Exception $e) {
            ActivityLog::create([
                'tenant_id'      => $tenant->id,
                'application_id' => $app->id,
                'action'         => 'decrypt',
                'ip_address'     => $request->ip(),
                'status'         => 'failed',
                'message'        => $e->getMessage(),
                'performed_at'   => now(),
            ]);

            return response()->json(['error' => 'Erreur de déchiffrement : ' . $e->getMessage()], 500);
        }
    }

    public function me(Request $request)
    {
        $apiKey = $this->resolveApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API invalide ou révoquée.'], 401);
        }

        $app    = $apiKey->application;
        $tenant = $app->tenant;
        $plan   = $tenant->subscription->plan ?? null;

        return response()->json([
            'success'     => true,
            'application' => $app->name,
            'tenant'      => $tenant->name,
            'plan'        => $plan->name ?? 'Free',
            'stats'       => [
                'total_encryptions' => $app->total_encryptions,
                'total_decryptions' => $app->total_decryptions,
                'max_ops_per_month' => $plan->max_ops_per_month ?? '∞',
            ],
            'status'      => $app->status,
        ]);
    }
}