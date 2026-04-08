<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKey extends Model
{
    protected $fillable = [
        'tenant_id', 'key_value', 'is_active'
    ];

    protected $hidden = [
        'key_value' // on ne l'expose jamais dans les réponses JSON
    ];

    // Une master key appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}