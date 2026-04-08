<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'role', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    // Un user appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Vérifier si c'est un owner
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    // Vérifier si c'est un admin
    public function isAdmin()
    {
        return in_array($this->role, ['owner', 'admin']);
    }
}