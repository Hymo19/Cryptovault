<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKey extends Model
{
    protected $fillable = [
        'tenant_id',
        'key_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}