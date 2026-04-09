<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE activity_logs DROP CONSTRAINT activity_logs_action_check");
        DB::statement("ALTER TABLE activity_logs ADD CONSTRAINT activity_logs_action_check 
            CHECK (action IN (
                'encrypt', 'decrypt', 'rotate', 'init', 'login', 'revoke',
                'create_app', 'delete_app', 'suspend_app', 'activate_app',
                'generate_key', 'revoke_key'
            ))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activity_logs DROP CONSTRAINT activity_logs_action_check");
        DB::statement("ALTER TABLE activity_logs ADD CONSTRAINT activity_logs_action_check 
            CHECK (action IN ('encrypt', 'decrypt', 'rotate', 'init', 'login', 'revoke'))
        ");
    }
};