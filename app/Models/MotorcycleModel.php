<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MotorcycleModel extends Model
{
    protected $fillable = [
        'brand',
        'model_name',
        'year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ProductCatalog::class, 'motorcycle_product', 'motorcycle_model_id', 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute(): string
    {
        return $this->brand . ' ' . $this->model_name . ($this->year ? ' (' . $this->year . ')' : '');
    }
}
