<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'image_path',
        'current_quantity',
        'unit_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'unit_price' => 'integer',
        'current_quantity' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
