<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'status',
        'total_encryptions',
        'total_decryptions',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function activeApiKey()
    {
        return $this->hasOne(ApiKey::class)->where('status', 'active')->latest();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}