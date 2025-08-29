<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'notes',
        'paid_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order items for the order (alias for items).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            default => 'Desconhecido',
        };
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'text-yellow-600',
            'pago' => 'text-blue-600',
            'entregue' => 'text-green-600',
            'cancelado' => 'text-red-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get the status badge color attribute.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'bg-yellow-100 text-yellow-800',
            'pago' => 'bg-blue-100 text-blue-800',
            'entregue' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pendente', 'pago']);
    }

    /**
     * Check if the order can be marked as delivered.
     */
    public function canBeDelivered(): bool
    {
        return $this->status === 'pago';
    }

    /**
     * Check if the order can be marked as paid.
     */
    public function canBePaid(): bool
    {
        return $this->status === 'pendente';
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'PEN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}
