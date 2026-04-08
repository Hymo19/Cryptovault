<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'email', 'slug', 'status', 'trial_ends_at'
    ];

    // Génère automatiquement le slug depuis le nom
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tenant) {
            $tenant->slug = Str::slug($tenant->name) . '-' . Str::random(6);
        });
    }

    // Un tenant a plusieurs utilisateurs
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Un tenant a plusieurs applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // Un tenant a plusieurs master keys
    public function masterKeys()
    {
        return $this->hasMany(MasterKey::class);
    }

    // La master key active du tenant
    public function activeMasterKey()
    {
        return $this->hasOne(MasterKey::class)->where('is_active', true);
    }

    // Un tenant a plusieurs logs
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Un tenant a un abonnement actif
    public function subscription()
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->latest();
    }
}