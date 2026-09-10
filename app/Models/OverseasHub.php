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
        'name',
        'hub_code',
        'code',
        'location',
        'city',
        'airport_name',
        'country',
        'coverage_countries',
        'service_routes',
        'mode_type',
        'hub_type',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'sort_order' => 'integer',
        'coverage_countries' => 'array',
        'service_routes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($hub) {
            if (empty($hub->hub_name) && !empty($hub->name)) {
                $hub->hub_name = $hub->name;
            }
            if (empty($hub->hub_code) && !empty($hub->code)) {
                $hub->hub_code = strtoupper($hub->code);
            }
            if (empty($hub->location)) {
                $hub->location = $hub->city ?? ($hub->country ? $hub->country . ' Gateway' : 'Transit Hub');
            }
            if (empty($hub->hub_type)) {
                $hub->hub_type = 'main_hub';
            }
            if (empty($hub->address)) {
                $hub->address = $hub->airport_name ?? ($hub->location . ' Air Cargo Terminal');
            }
        });
    }

    public function getNameAttribute()
    {
        return $this->attributes['hub_name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['hub_name'] = $value;
    }

    public function getCodeAttribute()
    {
        return $this->attributes['hub_code'] ?? null;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['hub_code'] = strtoupper($value);
    }

    public function getCityAttribute()
    {
        return $this->attributes['location'] ?? null;
    }

    public function setCityAttribute($value)
    {
        if (empty($this->attributes['location'])) {
            $this->attributes['location'] = $value;
        }
    }

    public function getAirportNameAttribute()
    {
        return $this->attributes['address'] ?? null;
    }

    public function setAirportNameAttribute($value)
    {
        if (empty($this->attributes['address'])) {
            $this->attributes['address'] = $value;
        }
    }

    public function getCoverageCountriesAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }
        $parts = preg_split('/[,\r\n]+/', (string) $value);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    public function setCoverageCountriesAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['coverage_countries'] = json_encode([]);
            return;
        }

        if (is_array($value)) {
            $clean = array_values(array_filter(array_map('trim', $value)));
            $this->attributes['coverage_countries'] = json_encode($clean);
            return;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $clean = array_values(array_filter(array_map('trim', $decoded)));
                $this->attributes['coverage_countries'] = json_encode($clean);
                return;
            }

            $parts = preg_split('/[,\r\n]+/', $trimmed);
            $clean = array_values(array_filter(array_map('trim', $parts)));
            $this->attributes['coverage_countries'] = json_encode($clean);
            return;
        }

        $this->attributes['coverage_countries'] = json_encode([]);
    }

    public function getServiceRoutesAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }
        $parts = preg_split('/[,\r\n]+/', (string) $value);
        return array_values(array_filter(array_map('trim', $parts)));
    }

    public function setServiceRoutesAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['service_routes'] = null;
            return;
        }

        if (is_array($value)) {
            $clean = array_values(array_filter(array_map('trim', $value)));
            $this->attributes['service_routes'] = implode(', ', $clean);
            return;
        }

        $this->attributes['service_routes'] = trim((string) $value);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function agencies()
    {
        return $this->hasMany(Agency::class, 'hub_id');
    }

    public function mawbs()
    {
        return $this->hasMany(MAWB::class, 'hub_id');
    }

    public function lastMileCarriers()
    {
        return $this->hasMany(LastMileCarrier::class, 'hub_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'current_hub_id');
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

    public function getCoverageSummaryAttribute(): string
    {
        if (!empty($this->coverage_countries) && is_array($this->coverage_countries)) {
            return implode(', ', array_slice($this->coverage_countries, 0, 4)) . (count($this->coverage_countries) > 4 ? ' +' . (count($this->coverage_countries) - 4) . ' more' : '');
        }

        return $this->country ?? $this->location ?? 'Global';
    }
}