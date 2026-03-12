<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['title','message','created_by','status', 'published_at', 'expires_at'];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getDisplayStatusAttribute()
    {
        if ($this->status !== 'active') {
            return ucfirst($this->status);
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        return 'Active';
    }
}
