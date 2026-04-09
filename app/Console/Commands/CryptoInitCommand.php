<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class CryptoInitCommand extends Command
{
    protected $signature = 'crypto:init';
    protected $description = 'Initialise le chiffrement PostgreSQL (DEK + Master Key + rotation)';

    public function handle()
    {
        $this->info('🚀 Initialisation du chiffrement PostgreSQL...');
        $this->newLine();

        // 1. Activation pgcrypto
        DB::statement("CREATE EXTENSION IF NOT EXISTS pgcrypto;");
        $this->info('✅ Extension pgcrypto activée');

        // 2. Récupérer le premier tenant
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->error('❌ Aucun tenant trouvé. Crée d\'abord un compte via /register');
            return;
        }

        // 3. Table master_keys
        DB::statement("
            CREATE TABLE IF NOT EXISTS master_keys (
                id SERIAL PRIMARY KEY,
                tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
                key_value TEXT NOT NULL,
                is_active BOOLEAN DEFAULT false,
                created_at TIMESTAMP DEFAULT NOW(),
                updated_at TIMESTAMP DEFAULT NOW()
            );
        ");
        $this->info('✅ Table master_keys créée / mise à jour');

        // 4. Première Master Key
        $hasKey = DB::select("SELECT COUNT(*) as count FROM master_keys WHERE tenant_id = ? AND is_active = true", [$tenant->id]);
        if ($hasKey[0]->count == 0) {
            $key = bin2hex(random_bytes(32));
            DB::insert("INSERT INTO master_keys (tenant_id, key_value, is_active, created_at, updated_at) VALUES (?, ?, true, NOW(), NOW())", [$tenant->id, $key]);
            $this->info("✅ Première Master Key générée pour le tenant {$tenant->name}");
        } else {
            $this->info("ℹ️ Master Key déjà existante pour {$tenant->name}");
        }

        // 5. Fonction encrypt_card_secure
        DB::statement("
            CREATE OR REPLACE FUNCTION encrypt_card_secure(data TEXT)
            RETURNS TABLE(encrypted BYTEA, encrypted_dek BYTEA, version INT) AS \$\$
            DECLARE
                dek BYTEA;
                master TEXT;
                current_version INT;
            BEGIN
                dek := gen_random_bytes(32);
                SELECT key_value, id INTO master, current_version
                FROM master_keys
                WHERE is_active = true
                ORDER BY id DESC LIMIT 1;

                RETURN QUERY
                SELECT
                    pgp_sym_encrypt(data, encode(dek, 'base64')),
                    pgp_sym_encrypt(encode(dek, 'base64'), master),
                    current_version;
            END;
            \$\$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction encrypt_card_secure créée');

        // 6. Fonction decrypt_card_secure
        DB::statement("
            CREATE OR REPLACE FUNCTION decrypt_card_secure(encrypted BYTEA, encrypted_dek BYTEA, version INT)
            RETURNS TEXT AS \$\$
            DECLARE
                master TEXT;
                dek TEXT;
            BEGIN
                SELECT key_value INTO master FROM master_keys WHERE id = version;
                dek := pgp_sym_decrypt(encrypted_dek, master);
                RETURN pgp_sym_decrypt(encrypted, dek);
            END;
            \$\$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction decrypt_card_secure créée');

        // 7. Fonction rotate_master_key
        DB::statement("
            CREATE OR REPLACE FUNCTION rotate_master_key(new_key TEXT)
            RETURNS JSON AS \$\$
            DECLARE
                old_version INT;
                new_version INT;
                v_tenant_id BIGINT;
            BEGIN
                SELECT id, tenant_id INTO old_version, v_tenant_id
                FROM master_keys WHERE is_active = true ORDER BY id DESC LIMIT 1;

                UPDATE master_keys SET is_active = false WHERE is_active = true;

                INSERT INTO master_keys (tenant_id, key_value, is_active, created_at, updated_at)
                VALUES (v_tenant_id, new_key, true, NOW(), NOW());

                SELECT id INTO new_version FROM master_keys WHERE is_active = true ORDER BY id DESC LIMIT 1;

                RETURN json_build_object(
                    'new_version', new_version,
                    'old_version', old_version,
                    'total_rotated', 0
                );
            END;
            \$\$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction rotate_master_key créée');

        // 8. Fonction get_rotation_status
        DB::statement("
            CREATE OR REPLACE FUNCTION get_rotation_status()
            RETURNS JSON AS \$\$
            DECLARE
                current_version INT;
                total_keys INT;
            BEGIN
                SELECT id INTO current_version FROM master_keys WHERE is_active = true ORDER BY id DESC LIMIT 1;
                SELECT COUNT(*) INTO total_keys FROM master_keys;

                RETURN json_build_object(
                    'current_version', current_version,
                    'total_keys', total_keys,
                    'up_to_date', total_keys,
                    'orphan_cards', 0
                );
            END;
            \$\$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction get_rotation_status créée');

        $this->newLine();
        $this->info('🎉 INITIALISATION TERMINÉE AVEC SUCCÈS !');
    }
}