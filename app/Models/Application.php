<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'description', 'status',
        'total_encryptions', 'total_decryptions', 'last_used_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    // Une app appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Une app a plusieurs API keys
    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    // La clé API active de l'app
    public function activeApiKey()
    {
        return $this->hasOne(ApiKey::class)
                    ->where('status', 'active')
                    ->latest();
    }

    // Une app a plusieurs logs
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}