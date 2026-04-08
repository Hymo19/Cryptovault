<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'application_id', 'tenant_id', 'key',
        'status', 'last_used_at', 'expires_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // Génère automatiquement la clé API
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($apiKey) {
            $apiKey->key = 'cv_key_' . Str::random(32);
        });
    }

    // Une API key appartient à une application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    // Une API key appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Vérifier si la clé est valide
    public function isValid()
    {
        if ($this->status !== 'active') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }
}