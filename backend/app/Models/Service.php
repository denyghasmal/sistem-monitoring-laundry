<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'price_per_unit',
        'unit',
        'estimated_hours',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'price_per_unit' => 'float',
        'estimated_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

