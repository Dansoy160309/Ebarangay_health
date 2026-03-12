<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class HealthWorker extends User
{
    protected $attributes = [
        'role' => 'health_worker',
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'health_worker');
        });
    }
}
