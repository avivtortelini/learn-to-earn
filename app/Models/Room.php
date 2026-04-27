<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = [
        'number',
        'monthly_price',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'integer',
        ];
    }

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class);
    }

    public function activeOccupancy(): HasOne
    {
        return $this->hasOne(Occupancy::class)->whereNull('ended_at')->latestOfMany();
    }

    public function getStatusAttribute(): string
    {
        return $this->activeOccupancy ? 'Terisi' : 'Kosong';
    }
}
