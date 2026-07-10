<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseShelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'warehouse_code',
        'warehouse_index',
        'slot_index',
        'sort_order',
        'name',
        'products',
        'archived',
    ];

    protected $casts = [
        'products' => 'array',
        'archived' => 'boolean',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
