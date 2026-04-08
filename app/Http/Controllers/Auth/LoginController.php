<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Afficher le formulaire de connexion
    public function showForm()
    {
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Tentative de connexion
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
    $request->session()->regenerate();
    Auth::user()->update(['last_login_at' => now()]);

    \App\Models\ActivityLog::create([
        'tenant_id'    => Auth::user()->tenant_id,
        'action'       => 'login',
        'ip_address'   => $request->ip(),
        'status'       => 'success',
        'message'      => 'Connexion réussie',
        'performed_at' => now(),
    ]);

    // Super admin → panneau admin
    if (Auth::user()->is_super_admin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
}

        // Échec de connexion
        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->withInput($request->only('email'));
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}