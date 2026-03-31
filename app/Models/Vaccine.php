<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manufacturer',
        'description',
        'storage_temp_range',
        'min_stock_level',
        'is_active',
    ];

    public function batches()
    {
        return $this->hasMany(VaccineBatch::class);
    }

    public function administrations()
    {
        return $this->hasMany(VaccineAdministration::class);
    }

    public function getInStockQuantityAttribute()
    {
        return $this->batches()
            ->where('is_active', true)
            ->where('expiry_date', '>', now())
            ->sum('quantity_remaining');
    }
}
