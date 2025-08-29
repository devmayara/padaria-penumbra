<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'image_path',
        'current_quantity',
        'unit_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'unit_price' => 'decimal:2',
        'current_quantity' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the product's image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    /**
     * Get the product's thumbnail URL (same as image for now).
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->image_url;
    }

    /**
     * Check if product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->current_quantity > 0;
    }

    /**
     * Check if product is low in stock (less than 10 items).
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->current_quantity > 0 && $this->current_quantity < 10;
    }

    /**
     * Get stock status text.
     */
    public function getStockStatusTextAttribute(): string
    {
        if ($this->current_quantity === 0) {
            return 'Sem estoque';
        } elseif ($this->is_low_stock) {
            return 'Estoque baixo';
        } else {
            return 'Em estoque';
        }
    }

    /**
     * Get stock status color class.
     */
    public function getStockStatusColorAttribute(): string
    {
        if ($this->current_quantity === 0) {
            return 'text-red-600';
        } elseif ($this->is_low_stock) {
            return 'text-yellow-600';
        } else {
            return 'text-green-600';
        }
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->unit_price, 2, ',', '.');
    }

    /**
     * Get raw price for forms.
     */
    public function getRawPriceAttribute(): string
    {
        return number_format($this->unit_price, 2, '.', '');
    }
}
