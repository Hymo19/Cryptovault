<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncryptionController extends Controller
{
    public function encrypt(Request $request)
    {
        $request->validate(['data' => 'required|string|max:255']);

        try {
            $result = DB::selectOne("
                SELECT 
                    encode(encrypted, 'base64') as encrypted_base64,
                    encode(encrypted_dek, 'base64') as encrypted_dek_base64,
                    version
                FROM encrypt_card_secure(?)
                LIMIT 1
            ", [$request->data]);

            return response()->json([
                'success'          => true,
                'encrypted'        => str_replace("\n", "", $result->encrypted_base64),     // ← nettoyage
                'encrypted_dek'    => str_replace("\n", "", $result->encrypted_dek_base64), // ← nettoyage
                'version'          => $result->version,
                'message'          => 'Chiffrement réussi (DEK + Master Key)'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function decrypt(Request $request)
    {
        $request->validate([
            'encrypted'     => 'required|string',
            'encrypted_dek' => 'required|string',
            'version'       => 'required|integer',
        ]);

        try {
            // Nettoyage des sauts de ligne et espaces avant décodage
            $clean_encrypted = str_replace(["\n", "\r", " "], '', $request->encrypted);
            $clean_dek       = str_replace(["\n", "\r", " "], '', $request->encrypted_dek);

            $result = DB::selectOne("
                SELECT decrypt_card_secure(
                    decode(?, 'base64'),
                    decode(?, 'base64'),
                    ?
                ) AS numero_carte
            ", [$clean_encrypted, $clean_dek, $request->version]);

            return response()->json([
                'success'      => true,
                'numero_carte' => $result->numero_carte,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}