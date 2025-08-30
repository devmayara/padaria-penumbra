<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_number',
        'qr_code_path',
        'pdf_path',
        'status',
        'generated_at',
        'printed_at',
        'notes',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the ticket.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generate a unique ticket number.
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return "{$prefix}{$date}{$random}";
    }

    /**
     * Mark ticket as generated.
     */
    public function markAsGenerated(): void
    {
        $this->update([
            'status' => 'gerado',
            'generated_at' => now(),
        ]);
    }

    /**
     * Mark ticket as printed.
     */
    public function markAsPrinted(): void
    {
        $this->update([
            'status' => 'impresso',
            'printed_at' => now(),
        ]);
    }

    /**
     * Get the QR code URL.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    /**
     * Get the PDF URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_path) {
            return asset('storage/' . $this->pdf_path);
        }
        return null;
    }

    /**
     * Check if ticket is generated.
     */
    public function isGenerated(): bool
    {
        return $this->status === 'gerado';
    }

    /**
     * Check if ticket is printed.
     */
    public function isPrinted(): bool
    {
        return $this->status === 'impresso';
    }

    /**
     * Get status text in Portuguese.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'gerado' => 'Gerado',
            'impresso' => 'Impresso',
            default => 'Desconhecido',
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'bg-yellow-100 text-yellow-800',
            'gerado' => 'bg-blue-100 text-blue-800',
            'impresso' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
