<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverseasHub extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'hub_name',
        'hub_code',
        'location',
        'hub_type',
        'address',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    public function getHubTypeLabelAttribute()
    {
        $types = [
            'main_hub' => 'Main Hub',
            'transit_point' => 'Transit Point',
            'sorting_center' => 'Sorting Center',
            'delivery_hub' => 'Delivery Hub',
        ];
        return $types[$this->hub_type] ?? ucfirst($this->hub_type);
    }

    public function getHubTypeColorAttribute()
    {
        $colors = [
            'main_hub' => 'purple',
            'transit_point' => 'blue',
            'sorting_center' => 'orange',
            'delivery_hub' => 'green',
        ];
        return $colors[$this->hub_type] ?? 'gray';
    }
}