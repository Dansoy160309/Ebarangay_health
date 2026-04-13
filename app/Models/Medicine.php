<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'generic_name',
        'brand_name',
        'dosage_form',
        'strength',
        'stock',
        'reorder_level',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function distributions()
    {
        return $this->hasMany(MedicineDistribution::class);
    }

    public function supplies()
    {
        return $this->hasMany(MedicineSupply::class);
    }
}

