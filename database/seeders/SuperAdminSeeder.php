<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crée un tenant fictif pour le super admin
        $tenant = Tenant::create([
            'name'   => 'CryptoVault',
            'email'  => 'admin@cryptovault.com',
            'status' => 'active',
        ]);

        User::create([
            'tenant_id'      => $tenant->id,
            'name'           => 'Super Admin',
            'email'          => 'admin@cryptovault.com',
            'password'       => Hash::make('TonMotDePasseSecurisé'),
            'role'           => 'owner',
            'is_super_admin' => true,
        ]);
    }
}