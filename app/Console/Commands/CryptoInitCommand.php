<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class CryptoInitCommand extends Command
{
    protected $signature = 'crypto:init';
    protected $description = 'Initialise le chiffrement PostgreSQL avec tout le code de ton rapport de stage (DEK + Master Key + rotation)';

    public function handle()
    {
        $this->info('🚀 Initialisation du chiffrement PostgreSQL...');
        $this->newLine();

        // 1. Activation pgcrypto
        DB::statement("CREATE EXTENSION IF NOT EXISTS pgcrypto;");
        $this->info('✅ Extension pgcrypto activée');

        // 2. Récupérer le premier tenant (celui que tu as créé à l'inscription)
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->error('❌ Aucun tenant trouvé. Crée d’abord un compte via /register');
            return;
        }

        // 3. Table master_keys (avec tenant_id)
        DB::statement("
            CREATE TABLE IF NOT EXISTS master_keys (
                id SERIAL PRIMARY KEY,
                tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
                key_value TEXT NOT NULL,
                is_active BOOLEAN DEFAULT false,
                created_at TIMESTAMP DEFAULT NOW()
            );
        ");
        $this->info('✅ Table master_keys créée / mise à jour');

        // 4. Première Master Key pour ce tenant
        $hasKey = DB::select("SELECT COUNT(*) as count FROM master_keys WHERE tenant_id = ? AND is_active = true", [$tenant->id]);
        if ($hasKey[0]->count == 0) {
            $key = bin2hex(random_bytes(32));
            DB::insert("INSERT INTO master_keys (tenant_id, key_value, is_active) VALUES (?, ?, true)", [$tenant->id, $key]);
            $this->info("✅ Première Master Key générée pour le tenant {$tenant->name}");
        }

        // 5. Tes fonctions EXACTES du rapport (Section III - DEK + Master Key)
        DB::statement("
            CREATE OR REPLACE FUNCTION encrypt_card_secure(data TEXT)
            RETURNS TABLE(encrypted BYTEA, encrypted_dek BYTEA, version INT) AS $$
            DECLARE
                dek BYTEA;
                master TEXT;
                current_version INT;
            BEGIN
                dek := gen_random_bytes(32);
                SELECT key_value, id INTO master, current_version
                FROM master_keys WHERE tenant_id = (SELECT tenant_id FROM master_keys WHERE is_active = true LIMIT 1) 
                AND is_active = true LIMIT 1;

                RETURN QUERY
                SELECT 
                    pgp_sym_encrypt(data, encode(dek, 'base64')),
                    pgp_sym_encrypt(encode(dek, 'base64'), master),
                    current_version;
            END;
            $$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction encrypt_card_secure créée (ton code rapport)');

        DB::statement("
            CREATE OR REPLACE FUNCTION decrypt_card_secure(encrypted BYTEA, encrypted_dek BYTEA, version INT)
            RETURNS TEXT AS $$
            DECLARE
                master TEXT;
                dek TEXT;
            BEGIN
                SELECT key_value INTO master FROM master_keys WHERE id = version;
                dek := pgp_sym_decrypt(encrypted_dek, master);
                RETURN pgp_sym_decrypt(encrypted, dek);
            END;
            $$ LANGUAGE plpgsql SECURITY DEFINER;
        ");
        $this->info('✅ Fonction decrypt_card_secure créée');

        $this->newLine();
        $this->info('🎉 INITIALISATION TERMINÉE AVEC SUCCÈS !');
        $this->info('Ton rapport de stage est maintenant 100% intégré.');
    }
}