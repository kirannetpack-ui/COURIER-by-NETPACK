<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomesticRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'origin_zone_id',
        'destination_zone_id',
        'service_type',
        'service_name',
        'base_rate',
        'per_kg_rate',
        'per_km_rate',
        'minimum_rate',
        'logistical_charge',
        'additional_charge',
        'additional_charge_reason',
        'weight_from',
        'weight_to',
        'estimated_hours',
        'estimated_days',
        'estimated_km',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'per_kg_rate' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'minimum_rate' => 'decimal:2',
        'logistical_charge' => 'decimal:2',
        'additional_charge' => 'decimal:2',
        'weight_from' => 'decimal:2',
        'weight_to' => 'decimal:2',
        'estimated_hours' => 'integer',
        'estimated_days' => 'integer',
        'estimated_km' => 'integer',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    // Service type constants
    const SERVICE_FLASH = 'flash';
    const SERVICE_SAME_DAY = 'same_day';
    const SERVICE_STANDARD = 'standard';
    const SERVICE_HIMALAYAN = 'himalayan';

    const SERVICE_NAMES = [
        'flash' => 'FLASH',
        'same_day' => 'SAME DAY',
        'standard' => 'STANDARD',
        'himalayan' => 'HIMALAYAN',
    ];

    const SERVICE_ICONS = [
        'flash' => '⚡',
        'same_day' => '🕐',
        'standard' => '📦',
        'himalayan' => '🏔️',
    ];

    const SERVICE_DESCRIPTIONS = [
        'flash' => 'Ultra-fast delivery within 2-4 hours',
        'same_day' => 'Same day delivery within the city',
        'standard' => 'Next day delivery within the country',
        'himalayan' => 'Delivery to remote/hilly areas (2-3 days)',
    ];

    const SERVICE_COLORS = [
        'flash' => 'red',
        'same_day' => 'orange',
        'standard' => 'blue',
        'himalayan' => 'purple',
    ];

    const SERVICE_TIME = [
        'flash' => '2-4 hours',
        'same_day' => 'Same day',
        'standard' => '1-2 days',
        'himalayan' => '2-3 days',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function originZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'origin_zone_id');
    }

    public function destinationZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'destination_zone_id');
    }

    public function shipments()
    {
        return $this->hasMany(DomesticShipment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereDate('effective_from', '<=', now())
                     ->where(function($q) {
                         $q->whereDate('effective_to', '>=', now())
                           ->orWhereNull('effective_to');
                     });
    }

    public function scopeByService($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    public function scopeByZones($query, $originZoneId, $destinationZoneId)
    {
        return $query->where('origin_zone_id', $originZoneId)
                     ->where('destination_zone_id', $destinationZoneId);
    }

    public function scopeByWeight($query, $weight)
    {
        return $query->where('weight_from', '<=', $weight)
                     ->where('weight_to', '>=', $weight);
    }

    public function getServiceNameAttribute()
    {
        return self::SERVICE_NAMES[$this->service_type] ?? ucfirst($this->service_type);
    }

    public function getServiceIconAttribute()
    {
        return self::SERVICE_ICONS[$this->service_type] ?? '📦';
    }

    public function getServiceDescriptionAttribute()
    {
        return self::SERVICE_DESCRIPTIONS[$this->service_type] ?? '';
    }

    public function getServiceColorAttribute()
    {
        return self::SERVICE_COLORS[$this->service_type] ?? 'gray';
    }

    public function getServiceTimeAttribute()
    {
        return self::SERVICE_TIME[$this->service_type] ?? 'Varies';
    }

    public function getServiceBadgeAttribute()
    {
        $colors = [
            'flash' => 'red',
            'same_day' => 'orange',
            'standard' => 'blue',
            'himalayan' => 'purple',
        ];
        $color = $colors[$this->service_type] ?? 'gray';
        
        return "<span class='px-2 py-1 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>{$this->service_icon} {$this->service_name}</span>";
    }

    public function calculateRate($weight, $distance = null)
    {
        $baseRate = max($this->base_rate, $this->minimum_rate);
        $weightCharge = $this->per_kg_rate * $weight;
        $distanceCharge = $distance ? ($this->per_km_rate * $distance) : 0;
        
        $subtotal = $baseRate + $weightCharge + $distanceCharge;
        $total = $subtotal + $this->logistical_charge + $this->additional_charge;
        
        return [
            'base_rate' => $baseRate,
            'weight_charge' => $weightCharge,
            'distance_charge' => $distanceCharge,
            'logistical_charge' => $this->logistical_charge,
            'additional_charge' => $this->additional_charge,
            'subtotal' => $subtotal,
            'total' => $total,
            'breakdown' => [
                'weight' => $weight,
                'distance' => $distance,
                'per_kg_rate' => $this->per_kg_rate,
                'per_km_rate' => $this->per_km_rate,
                'minimum_rate' => $this->minimum_rate,
            ],
        ];
    }

    public static function getServiceTypes()
    {
        return [
            self::SERVICE_FLASH,
            self::SERVICE_SAME_DAY,
            self::SERVICE_STANDARD,
            self::SERVICE_HIMALAYAN,
        ];
    }

    public static function getServiceTypeOptions()
    {
        $options = [];
        foreach (self::getServiceTypes() as $type) {
            $options[$type] = [
                'name' => self::SERVICE_NAMES[$type],
                'icon' => self::SERVICE_ICONS[$type],
                'description' => self::SERVICE_DESCRIPTIONS[$type],
                'color' => self::SERVICE_COLORS[$type],
                'time' => self::SERVICE_TIME[$type],
            ];
        }
        return $options;
    }
}