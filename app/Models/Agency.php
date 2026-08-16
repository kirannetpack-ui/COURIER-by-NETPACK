<?php
// app/Models/Agency.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agency extends Authenticatable
{
    protected $fillable = [
        'name', 'code', 'country', 'city', 'address', 'phone', 'email', 'password', 'is_active'
    ];
    
    protected $hidden = ['password'];
    
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'current_agency_id');
    }
    
    public function getArrivedShipments()
    {
        return $this->shipments()->whereNotNull('arrived_at_agency')->whereNull('departed_from_agency');
    }
    
    public function getDepartedShipments()
    {
        return $this->shipments()->whereNotNull('departed_from_agency');
    }
}