<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCatalog extends Model
{
    use SoftDeletes;
    protected $table = 'product_catalog';

    protected $fillable = [
        'product_name',
        'brand',
        'product_description',
        'sku',
        'description',
        'qr_code_path',
        'status',
        'size',
        'color',
        'stock_quantity',
        'reorder_level',
        'warehouse',
        'manufacturing_date',
        'batch_lot_number',
        'expiration_date',
    ];

    protected $casts = [
        'status' => 'string',
        'manufacturing_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function motorcycleModels(): BelongsToMany
    {
        return $this->belongsToMany(MotorcycleModel::class, 'motorcycle_product', 'product_id', 'motorcycle_model_id');
    }

    public function stockProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }

    /**
     * Get effective reorder level — prefers the matching All Stocks (products) table value.
     */
    public function getEffectiveReorderLevelAttribute(): int
    {
        return $this->stockProduct?->reorder_level ?? $this->reorder_level ?? 10;
    }

    /**
     * Get effective stock quantity — prefers the matching All Stocks (products) table value.
     */
    public function getEffectiveStockQuantityAttribute(): int
    {
        return $this->stockProduct?->stock_quantity ?? $this->stock_quantity ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    public function scopeByProductDescription($query, $productDescription)
    {
        return $query->where('product_description', $productDescription);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByMotorcycle($query, $motorcycleModelId)
    {
        return $query->whereHas('motorcycleModels', function ($q) use ($motorcycleModelId) {
            $q->where('motorcycle_models.id', $motorcycleModelId);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('product_description', 'like', "%{$search}%");
        });
    }

    public function isUsedInOtherModules(): bool
    {
        // Check if product is used in inventory, purchase orders, sales, or warehouse transactions
        // This will be implemented based on your existing module structure
        return false;
    }
}
