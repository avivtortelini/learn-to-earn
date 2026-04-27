<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'identity_number',
        'phone',
        'address',
        'status',
    ];

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class);
    }

    public function activeOccupancy(): HasOne
    {
        return $this->hasOne(Occupancy::class)->whereNull('ended_at')->latestOfMany();
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
