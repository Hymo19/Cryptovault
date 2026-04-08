<?php

namespace App\Services;

use App\Models\MasterKey;
use App\Models\Tenant;
use Exception;

class EnvelopeEncryptionService
{
    /**
     * Initialiser une Master Key pour un tenant
     */
    public function initMasterKey(Tenant $tenant): MasterKey
    {
        // Désactiver l'ancienne master key
        $tenant->masterKeys()->update(['is_active' => false]);

        // Générer une nouvelle master key (256 bits)
        $rawKey = random_bytes(32);
        $encodedKey = base64_encode($rawKey);

        return MasterKey::create([
            'tenant_id' => $tenant->id,
            'key_value' => $encodedKey,
            'is_active' => true,
        ]);
    }

    /**
     * Chiffrer une donnée
     */
    public function encrypt(Tenant $tenant, string $plaintext): array
    {
        $masterKey = $tenant->activeMasterKey;

        if (!$masterKey) {
            throw new Exception('Aucune master key active pour ce tenant.');
        }

        // Générer une Data Encryption Key (DEK) aléatoire
        $dek = random_bytes(32);

        // Chiffrer la donnée avec la DEK (AES-256-GCM)
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $dek,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        // Chiffrer la DEK avec la Master Key (KEK)
        $masterKeyRaw = base64_decode($masterKey->key_value);
        $dekIv = random_bytes(12);
        $dekTag = '';
        $encryptedDek = openssl_encrypt(
            $dek,
            'aes-256-gcm',
            $masterKeyRaw,
            OPENSSL_RAW_DATA,
            $dekIv,
            $dekTag,
            '',
            16
        );

        return [
            'ciphertext'    => base64_encode($ciphertext),
            'iv'            => base64_encode($iv),
            'tag'           => base64_encode($tag),
            'encrypted_dek' => base64_encode($encryptedDek),
            'dek_iv'        => base64_encode($dekIv),
            'dek_tag'       => base64_encode($dekTag),
            'master_key_id' => $masterKey->id,
        ];
    }

    /**
     * Déchiffrer une donnée
     */
    public function decrypt(Tenant $tenant, array $payload): string
    {
        $masterKey = MasterKey::where('tenant_id', $tenant->id)
            ->where('id', $payload['master_key_id'])
            ->first();

        if (!$masterKey) {
            throw new Exception('Master key introuvable.');
        }

        $masterKeyRaw = base64_decode($masterKey->key_value);

        // Déchiffrer la DEK
        $dek = openssl_decrypt(
            base64_decode($payload['encrypted_dek']),
            'aes-256-gcm',
            $masterKeyRaw,
            OPENSSL_RAW_DATA,
            base64_decode($payload['dek_iv']),
            base64_decode($payload['dek_tag'])
        );

        if ($dek === false) {
            throw new Exception('Impossible de déchiffrer la DEK.');
        }

        // Déchiffrer la donnée
        $plaintext = openssl_decrypt(
            base64_decode($payload['ciphertext']),
            'aes-256-gcm',
            $dek,
            OPENSSL_RAW_DATA,
            base64_decode($payload['iv']),
            base64_decode($payload['tag'])
        );

        if ($plaintext === false) {
            throw new Exception('Impossible de déchiffrer la donnée.');
        }

        return $plaintext;
    }

    /**
     * Rotation de la Master Key
     */
    public function rotateMasterKey(Tenant $tenant): MasterKey
    {
        return $this->initMasterKey($tenant);
    }
}