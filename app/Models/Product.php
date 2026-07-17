<?php

namespace App\Models;

use App\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_name',
        'description',
        'sku',
        'barcode',
        'category',
        'brand',
        'warehouse',
        'size',
        'color',
        'unit_price',
        'stock_quantity',
        'reorder_level',
        'supplier_name',
        'last_restock_date',
        'expiry_date',
        'batch_lot_number',
        'manufacturing_date',
        'product_catalog_id',
        'is_active',
        'is_archived',
        'disposal_status',
        'disposal_date_identified',
        'disposal_date_disposed',
        'disposal_reason',
        'compatibility',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'last_restock_date' => 'date',
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'disposal_date_identified' => 'date',
        'disposal_date_disposed' => 'date',
    ];

    protected $appends = [
        'expiry_status',
        'expiry_status_label',
        'days_until_expiry',
    ];

    public function getTotalValueAttribute()
    {
        return $this->stock_quantity * $this->unit_price;
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'non_expiring';
        }

        $expiryDate = Carbon::parse($this->expiry_date);
        if ($expiryDate->isPast()) {
            return 'expired';
        }

        if ($expiryDate->lte(Carbon::today()->addDays(30))) {
            return 'expiring';
        }

        return 'ok';
    }

    public function getExpiryStatusLabelAttribute()
    {
        return match ($this->expiry_status) {
            'expired' => 'Expired',
            'expiring' => 'Expiring Soon',
            'non_expiring' => 'Non-expiring',
            default => 'Fresh',
        };
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return max(0, Carbon::today()->diffInDays($this->expiry_date));
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function productCatalog()
    {
        return $this->belongsTo(ProductCatalog::class, 'product_catalog_id');
    }

    public function warehouseStocks()
    {
        return $this->hasMany(ProductWarehouseStock::class);
    }
}
