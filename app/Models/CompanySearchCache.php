<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['search_hash', 'latitude', 'longitude', 'radius', 'category', 'response_data', 'consulted_at'])]
class CompanySearchCache extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'radius' => 'integer',
            'response_data' => 'array',
            'consulted_at' => 'datetime',
        ];
    }
}
