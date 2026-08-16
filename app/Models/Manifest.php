<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Manifest extends Model
{
    protected $fillable = [
        'manifest_number',
        'created_by',
        'partner_id',
        'load_type',
        'status',
        'origin_city',
        'destination_city',
        'current_location',
        'total_bags',
        'total_shipments',
        'total_weight',
        'dispatched_at',
        'received_at',
        'delivered_at',
        'metadata',
 'pod_uploaded_at',
    'pod_uploaded_by',
    'pod_file',
    'pod_notes',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
        'delivered_at' => 'datetime',
        'metadata' => 'array',
        'total_weight' => 'decimal:2',
    'pod_uploaded_at' => 'datetime',

    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function bags()
    {
        return $this->hasMany(ManifestBag::class);
    }

    public function shipments()
    {
        return $this->hasMany(ManifestShipment::class);
    }

    public function trackingLogs()
    {
        return $this->hasMany(ManifestTrackingLog::class);
    }

    public static function generateManifestNumber()
    {
        $prefix = 'MF';
        $date = date('Ymd');
        $random = Str::upper(Str::random(6));
        $manifestNumber = $prefix . '-' . $date . '-' . $random;
        
        while (self::where('manifest_number', $manifestNumber)->exists()) {
            $random = Str::upper(Str::random(6));
            $manifestNumber = $prefix . '-' . $date . '-' . $random;
        }
        
        return $manifestNumber;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_transit' => 'bg-blue-100 text-blue-800',
            'received' => 'bg-purple-100 text-purple-800',
            'dispatched' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'in_transit' => 'In Transit',
            'received' => 'Received',
            'dispatched' => 'Dispatched',
            'delivered' => 'Delivered',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function addTrackingLog($eventType, $description = null, $location = null, $bagId = null, $shipmentId = null)
    {
        return ManifestTrackingLog::create([
            'manifest_id' => $this->id,
            'bag_id' => $bagId,
            'shipment_id' => $shipmentId,
            'event_type' => $eventType,
            'location' => $location,
            'description' => $description,
            'performed_by' => auth()->id(),
        ]);
    }

public function podUploadedBy()
{
    return $this->belongsTo(User::class, 'pod_uploaded_by');
}

}