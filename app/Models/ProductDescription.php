<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    protected $fillable = [
        'name',
        'sku_prefix',
        'brands',
        'is_active',
    ];

    protected $casts = [
        'brands' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active product descriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the brands as an array
     */
    public function getBrandsListAttribute()
    {
        return $this->brands ?? [];
    }
}
