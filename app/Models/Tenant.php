<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'slug',
        'status',
        'trial_ends_at',
    ];

    // Ajoute ceci
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function masterKeys()
    {
        return $this->hasMany(MasterKey::class);
    }

    public function activeMasterKey()
    {
        return $this->hasOne(MasterKey::class)->where('is_active', true);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }
}