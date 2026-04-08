<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'status', 'starts_at', 'ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // Un abonnement appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Un abonnement appartient à un plan
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}