<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // on utilise performed_at à la place

    protected $fillable = [
        'tenant_id', 'application_id', 'action',
        'ip_address', 'status', 'message', 'performed_at'
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    // Un log appartient à un tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Un log appartient à une application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}