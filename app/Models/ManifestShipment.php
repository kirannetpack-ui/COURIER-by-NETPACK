<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManifestShipment extends Model
{
    protected $fillable = [
        'manifest_id',
        'bag_id',
        'shipment_id',
        'partner_id',
        'status',
        'delivery_type',
        'is_collected',
        'collected_at',
        'delivery_fee',
        'payment_status',
        'received_at',
        'delivered_at',
        'dispatched_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'is_collected' => 'boolean',
        'collected_at' => 'datetime',
        'received_at' => 'datetime',
        'delivered_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'delivery_fee' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class);
    }

    public function bag()
    {
        return $this->belongsTo(ManifestBag::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'received' => 'bg-blue-100 text-blue-800',
            'delivered' => 'bg-green-100 text-green-800',
            'dispatched' => 'bg-purple-100 text-purple-800',
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getDeliveryTypeLabelAttribute()
    {
        $labels = [
            'door_delivery' => '🏠 Door Delivery',
            'collection' => '🏢 Collection from Office',
        ];
        return $labels[$this->delivery_type] ?? $this->delivery_type;
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'pre_defined' => 'bg-blue-100 text-blue-800',
            'nil' => 'bg-gray-100 text-gray-800',
        ];
        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }
}