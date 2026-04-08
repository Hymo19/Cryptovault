<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Afficher le formulaire d'inscription
    public function showForm()
    {
        return view('auth.register');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8|confirmed',
        ], [
            'company_name.required' => 'Le nom de votre entreprise est obligatoire.',
            'name.required'         => 'Votre nom est obligatoire.',
            'email.required'        => 'L\'email est obligatoire.',
            'email.unique'          => 'Cet email est déjà utilisé.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'    => 'Les mots de passe ne correspondent pas.',
        ]);

        // 1. Créer le tenant (l'entreprise)
        $tenant = Tenant::create([
            'name'          => $request->company_name,
            'email'         => $request->email,
            'status'        => 'trial',
            'trial_ends_at' => now()->addDays(14), // 14 jours d'essai
        ]);

        // 2. Créer le user owner du tenant
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'owner',
        ]);

        // 3. Abonner au plan gratuit automatiquement
        $freePlan = Plan::where('name', 'Free')->first();
        if ($freePlan) {
            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id'   => $freePlan->id,
                'status'    => 'active',
                'starts_at' => now(),
            ]);
        }

        // 4. Connecter automatiquement
        Auth::login($user);

        // 5. Rediriger vers le dashboard
        return redirect()->route('dashboard')
                         ->with('success', 'Bienvenue sur CryptoVault ! Votre compte est créé.');
    }
}