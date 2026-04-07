<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'max_apps',
        'max_ops_per_month',
        'price',
        'description',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}