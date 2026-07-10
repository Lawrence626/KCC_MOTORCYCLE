<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shelves()
    {
        return $this->hasMany(WarehouseShelf::class, 'warehouse_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
