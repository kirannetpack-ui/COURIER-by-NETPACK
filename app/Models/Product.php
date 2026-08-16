<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'price',
        'price_npr',
        'discount_price',
        'sku',
        'stock_quantity',
        'is_active',
        'images',
        'weight',
        'dimensions',
        'features',
        'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'metadata' => 'array',
        'price' => 'decimal:2',
        'price_npr' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}