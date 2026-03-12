<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Patient extends User
{
    protected $attributes = [
        'role' => 'patient',
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'patient');
        });
    }
}
