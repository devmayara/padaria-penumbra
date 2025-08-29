<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'unit_price',
        'reason',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that created the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movement type text.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'entrada' => 'Entrada',
            'saida' => 'Saída',
            'ajuste' => 'Ajuste',
            default => 'Desconhecido'
        };
    }

    /**
     * Get the movement type color class.
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'entrada' => 'text-green-600',
            'saida' => 'text-red-600',
            'ajuste' => 'text-yellow-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get the movement type badge color.
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'entrada' => 'bg-green-100 text-green-800',
            'saida' => 'bg-red-100 text-red-800',
            'ajuste' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the quantity with sign.
     */
    public function getQuantityWithSignAttribute(): string
    {
        $sign = $this->type === 'entrada' ? '+' : '-';
        return $sign . abs($this->quantity);
    }

    /**
     * Get the total value of the movement.
     */
    public function getTotalValueAttribute(): float
    {
        if ($this->unit_price) {
            return $this->quantity * $this->unit_price;
        }
        return 0;
    }

    /**
     * Get formatted total value.
     */
    public function getFormattedTotalValueAttribute(): string
    {
        return 'R$ ' . number_format($this->total_value, 2, ',', '.');
    }
}
