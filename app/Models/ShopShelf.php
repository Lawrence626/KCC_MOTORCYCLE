<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopShelf extends Model
{
    protected $fillable = [
        'name',
        'location',
        'capacity',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shopInventory()
    {
        return $this->hasMany(ShopInventory::class);
    }

    public function getOccupiedAttribute()
    {
        return $this->shopInventory()->count();
    }

    public function getAvailableAttribute()
    {
        return max(0, $this->capacity - $this->occupied);
    }
}
