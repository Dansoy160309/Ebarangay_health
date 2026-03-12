<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Doctor extends User
{
    protected $attributes = [
        'role' => 'doctor',
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'doctor');
        });
    }
}
