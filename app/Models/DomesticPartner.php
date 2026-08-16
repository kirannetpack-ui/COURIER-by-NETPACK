<?php
// app/Models/DomesticPartner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DomesticPartner extends Authenticatable
{
    protected $table = 'domestic_partners';
    
    protected $fillable = [
        'name', 'code', 'company_name', 'email', 'password', 'phone', 'address',
        'city', 'district', 'province', 'pan_number', 'service_type', 'service_areas',
        'margin_percentage', 'is_active', 'kyc_verified'
    ];
    
    protected $hidden = ['password'];
    
    protected $casts = [
        'service_areas' => 'array',
        'margin_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'kyc_verified' => 'boolean'
    ];
    
    public function zones()
    {
        return $this->hasMany(PartnerZone::class);
    }
    
    public function rates()
    {
        return $this->hasMany(PartnerRate::class);
    }
    
    public function staff()
    {
        return $this->hasMany(PartnerStaff::class);
    }
    
    public function calculatePrice($zoneId, $serviceTier, $weight, $distance)
    {
        $rate = $this->rates()
            ->where('zone_id', $zoneId)
            ->where('service_tier', $serviceTier)
            ->first();
        
        if (!$rate) return null;
        
        $subtotal = $rate->base_rate + ($rate->per_kg_rate * $weight) + ($rate->per_km_rate * $distance);
        $total = $subtotal + $rate->logistical_charge + $rate->additional_charge;
        $finalPrice = $total + ($total * $this->margin_percentage / 100);
        
        return [
            'subtotal' => round($subtotal, 2),
            'logistical_charge' => $rate->logistical_charge,
            'additional_charge' => $rate->additional_charge,
            'partner_margin' => round($total * $this->margin_percentage / 100, 2),
            'total' => round($finalPrice, 2),
            'breakdown' => [
                'base_rate' => $rate->base_rate,
                'per_kg_charge' => round($rate->per_kg_rate * $weight, 2),
                'per_km_charge' => round($rate->per_km_rate * $distance, 2),
                'logistical_charge' => $rate->logistical_charge,
                'additional_charge' => $rate->additional_charge,
                'margin' => round($total * $this->margin_percentage / 100, 2)
            ]
        ];
    }
}