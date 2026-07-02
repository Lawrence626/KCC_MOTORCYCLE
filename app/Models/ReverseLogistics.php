<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReverseLogistics extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'warehouse',
        'return_reason',
        'condition',
        'source',
        'status',
        'reported_date',
        'notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
