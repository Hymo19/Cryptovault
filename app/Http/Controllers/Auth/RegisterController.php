<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'name'         => 'required|string|max:255',
            'password'     => 'required|min:8|confirmed',
        ]);

        // Créer le tenant
        $tenant = Tenant::create([
            'name'          => $request->company_name,
            'email'         => $request->email,
            'slug'          => Str::slug($request->company_name) . '-' . Str::random(5),
            'status'        => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Créer l'utilisateur owner
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'owner',
        ]);

        // Abonnement plan gratuit automatique
        $freePlan = Plan::where('name', 'Free')->first();
        if ($freePlan) {
            Subscription::create([
                'tenant_id'  => $tenant->id,
                'plan_id'    => $freePlan->id,
                'starts_at'  => now(),
                'status'     => 'active',
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}