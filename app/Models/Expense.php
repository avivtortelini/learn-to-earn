<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'description',
        'amount',
        'spent_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'spent_at' => 'date',
        ];
    }
}
