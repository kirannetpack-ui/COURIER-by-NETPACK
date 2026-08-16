<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'zone_name',
        'zone_code',
        'zone_type',
        'districts',
        'municipalities',
        'wards',
        'description',
        'is_active',
        // Rate fields for each service
        'flash_base_rate',
        'flash_per_kg_rate',
        'flash_estimated_hours',
        'same_day_base_rate',
        'same_day_per_kg_rate',
        'same_day_estimated_hours',
        'standard_base_rate',
        'standard_per_kg_rate',
        'standard_estimated_hours',
        'himalayan_base_rate',
        'himalayan_per_kg_rate',
        'himalayan_estimated_hours',
        // Admin margin fields
        'admin_margin_type',
        'admin_margin_value',
    ];

    protected $casts = [
        'districts' => 'array',
        'municipalities' => 'array',
        'wards' => 'array',
        'is_active' => 'boolean',
        'flash_base_rate' => 'decimal:2',
        'flash_per_kg_rate' => 'decimal:2',
        'same_day_base_rate' => 'decimal:2',
        'same_day_per_kg_rate' => 'decimal:2',
        'standard_base_rate' => 'decimal:2',
        'standard_per_kg_rate' => 'decimal:2',
        'himalayan_base_rate' => 'decimal:2',
        'himalayan_per_kg_rate' => 'decimal:2',
        'admin_margin_value' => 'decimal:2',
    ];

    /**
     * Get the partner that owns this zone
     */
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

/**
 * Get the admin who created this zone
 */
public function admin()
{
    return $this->belongsTo(User::class, 'admin_id');
}

/**
 * Scope for partner zones
 */
public function scopePartnerZones($query, $partnerId)
{
    return $query->where('partner_id', $partnerId);
}

/**
 * Scope for admin zones
 */
public function scopeAdminZones($query)
{
    return $query->whereNull('partner_id');
}
    /**
     * Get the domestic rates where this zone is the origin
     */
    public function originRates()
    {
        return $this->hasMany(DomesticRate::class, 'origin_zone_id');
    }

    /**
     * Get the domestic rates where this zone is the destination
     */
    public function destinationRates()
    {
        return $this->hasMany(DomesticRate::class, 'destination_zone_id');
    }

    /**
     * Get service rates for a specific service type
     */
    public function getServiceRates($serviceType)
    {
        $serviceMap = [
            'flash' => ['base' => 'flash_base_rate', 'per_kg' => 'flash_per_kg_rate', 'hours' => 'flash_estimated_hours'],
            'same_day' => ['base' => 'same_day_base_rate', 'per_kg' => 'same_day_per_kg_rate', 'hours' => 'same_day_estimated_hours'],
            'standard' => ['base' => 'standard_base_rate', 'per_kg' => 'standard_per_kg_rate', 'hours' => 'standard_estimated_hours'],
            'himalayan' => ['base' => 'himalayan_base_rate', 'per_kg' => 'himalayan_per_kg_rate', 'hours' => 'himalayan_estimated_hours'],
        ];

        if (!isset($serviceMap[$serviceType])) {
            return null;
        }

        $fields = $serviceMap[$serviceType];
        return [
            'base_rate' => $this->{$fields['base']} ?? 0,
            'per_kg_rate' => $this->{$fields['per_kg']} ?? 0,
            'estimated_hours' => $this->{$fields['hours']} ?? null,
        ];
    }

    /**
     * Get zone type label
     */
    public function getZoneTypeLabelAttribute()
    {
        $types = [
            'urban' => '🏙️ Urban',
            'semi_urban' => '🏘️ Semi-Urban',
            'rural' => '🌾 Rural',
            'hilly' => '⛰️ Hilly',
            'himalayan' => '🏔️ Himalayan',
        ];
        return $types[$this->zone_type] ?? $this->zone_type;
    }

    /**
     * Get districts as comma separated string
     */
    public function getDistrictsListAttribute()
    {
        if (is_array($this->districts)) {
            return implode(', ', $this->districts);
        }
        return $this->districts;
    }

    /**
     * Scope for active zones
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for zones by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('zone_type', $type);
    }

/**
 * Boot the model
 */
protected static function booted()
{
    static::updated(function ($zone) {
        // Check if rates were changed
        $rateFields = [
            'flash_base_rate', 'flash_per_kg_rate', 'flash_estimated_hours',
            'same_day_base_rate', 'same_day_per_kg_rate', 'same_day_estimated_hours',
            'standard_base_rate', 'standard_per_kg_rate', 'standard_estimated_hours',
            'himalayan_base_rate', 'himalayan_per_kg_rate', 'himalayan_estimated_hours',
        ];
        
        $changes = [];
        foreach ($rateFields as $field) {
            if ($zone->isDirty($field)) {
                $changes[$field] = [
                    'old' => $zone->getOriginal($field),
                    'new' => $zone->$field
                ];
            }
        }
        
        if (!empty($changes)) {
            $zone->notifyAdminAboutRateChange($changes);
        }
    });
}

/**
 * Notify admins about rate changes
 */
public function notifyAdminAboutRateChange($changes)
{
    $admins = \App\Models\User::whereIn('user_type', ['admin', 'super_admin', 'domestic_admin'])->get();
    
    $partner = $this->partner;
    $message = "📋 RATE CHANGE NOTIFICATION\n\n";
    $message .= "Partner: {$partner->name} ({$partner->email})\n";
    $message .= "Zone: {$this->zone_name}\n";
    $message .= "Zone Code: {$this->zone_code}\n\n";
    $message .= "Changes made:\n";
    
    $fieldLabels = [
        'flash_base_rate' => 'Flash Base Rate',
        'flash_per_kg_rate' => 'Flash Per KG Rate',
        'flash_estimated_hours' => 'Flash Estimated Hours',
        'same_day_base_rate' => 'Same Day Base Rate',
        'same_day_per_kg_rate' => 'Same Day Per KG Rate',
        'same_day_estimated_hours' => 'Same Day Estimated Hours',
        'standard_base_rate' => 'Standard Base Rate',
        'standard_per_kg_rate' => 'Standard Per KG Rate',
        'standard_estimated_hours' => 'Standard Estimated Hours',
        'himalayan_base_rate' => 'Himalayan Base Rate',
        'himalayan_per_kg_rate' => 'Himalayan Per KG Rate',
        'himalayan_estimated_hours' => 'Himalayan Estimated Hours',
    ];
    
    foreach ($changes as $field => $values) {
        $label = $fieldLabels[$field] ?? $field;
        $oldValue = $values['old'] ?? 'N/A';
        $newValue = $values['new'] ?? 'N/A';
        $message .= "  • {$label}: {$oldValue} → {$newValue}\n";
    }
    
    $message .= "\nReviewed at: " . now()->format('Y-m-d H:i:s');
    
    // Log the notification
    foreach ($admins as $admin) {
        \App\Models\ReminderLog::create([
            'pickup_request_id' => null,
            'reminder_id' => null,
            'reminder_type' => 'admin_alert',
            'sent_to' => $admin->email,
            'message' => $message,
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'zone_id' => $this->id,
                'partner_id' => $this->partner_id,
                'changes' => $changes,
            ]
        ]);
    }
    
    // Also send email notification
    $this->sendRateChangeEmail($admins, $message);
}

/**
 * Send rate change email to admins
 */
private function sendRateChangeEmail($admins, $message)
{
    try {
        foreach ($admins as $admin) {
            // You can implement actual email sending here
            // Mail::to($admin->email)->send(new RateChangeNotification($message));
            Log::info('Rate change notification sent to admin', [
                'admin_email' => $admin->email,
                'zone_id' => $this->id,
                'partner_id' => $this->partner_id
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Failed to send rate change email', [
            'error' => $e->getMessage(),
            'zone_id' => $this->id
        ]);
    }
}

}