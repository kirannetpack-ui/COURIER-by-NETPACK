<?php

namespace App\Services;

use App\Models\DomesticPartner;
use App\Models\RiderJobOffer;
use App\Models\RiderProfile;
use App\Models\RiderRateRule;
use App\Models\ShipmentAssignment;
use App\Models\User;
use Illuminate\Support\Str;

class EcommerceDispatchService
{
    /**
     * Get or create active rider rate rule
     */
    public function getActiveRateRule(string $vehicleType = 'motorcycle'): RiderRateRule
    {
        return RiderRateRule::firstOrCreate(
            ['vehicle_type' => $vehicleType, 'is_active' => true],
            [
                'zone_name' => 'Kathmandu Valley',
                'base_distance_km' => 3.00,
                'base_rate' => 80.00,
                'additional_km_rate' => 15.00,
                'weight_surcharge_per_kg' => 10.00,
                'cod_handling_fee' => 10.00,
                'return_fee' => 60.00,
                'is_active' => true,
            ]
        );
    }

    /**
     * Create Direct Rider Delivery (Seller -> Rider -> Customer)
     */
    public function createDirectDelivery(User $seller, array $data): ShipmentAssignment
    {
        $masterAwb = 'NPK-ECOM-' . date('ymd') . '-' . strtoupper(Str::random(5));
        $distanceKm = (float) ($data['distance_km'] ?? 5.00);
        $weightKg = (float) ($data['parcel_weight'] ?? 1.00);
        $codAmount = (float) ($data['cod_amount'] ?? 0.00);
        $vehicleType = $data['vehicle_type'] ?? 'motorcycle';

        $rateRule = $this->getActiveRateRule($vehicleType);
        $fee = $rateRule->calculateFee($distanceKm, $weightKg, $codAmount);

        $pickupOtp = ShipmentAssignment::generateOtp();
        $deliveryOtp = ShipmentAssignment::generateOtp();

        $assignment = ShipmentAssignment::create([
            'master_awb' => $masterAwb,
            'assignment_type' => 'local_direct',
            'provider_type' => 'rider',
            'sequence' => 1,
            'pickup_name' => $data['pickup_name'] ?? $seller->business_name ?? $seller->name,
            'pickup_phone' => $data['pickup_phone'] ?? $seller->phone ?? '9800000000',
            'pickup_address' => $data['pickup_address'] ?? $seller->address ?? 'Kathmandu Hub',
            'pickup_lat' => $data['pickup_lat'] ?? 27.7172,
            'pickup_lng' => $data['pickup_lng'] ?? 85.3240,
            'delivery_name' => $data['delivery_name'],
            'delivery_phone' => $data['delivery_phone'],
            'delivery_address' => $data['delivery_address'],
            'delivery_lat' => $data['delivery_lat'] ?? null,
            'delivery_lng' => $data['delivery_lng'] ?? null,
            'distance_km' => $distanceKm,
            'parcel_weight' => $weightKg,
            'provider_fee' => $fee,
            'cod_amount' => $codAmount,
            'status' => 'assigned',
            'current_custody' => 'Seller (' . ($seller->business_name ?? $seller->name) . ')',
            'pickup_otp' => $pickupOtp,
            'delivery_otp' => $deliveryOtp,
            'assigned_at' => now(),
        ]);

        // Broadcast offer to verified nearby riders
        $this->broadcastOffers($assignment);

        return $assignment;
    }

    /**
     * Create Multi-Leg Hybrid Delivery (Seller -> First-Mile Rider -> Origin Hub -> Domestic Partner Linehaul -> Dest Hub -> Last-Mile Rider -> Customer)
     */
    public function createMultiLegDelivery(User $seller, array $data, ?DomesticPartner $partner = null): array
    {
        $masterAwb = 'NPK-HYB-' . date('ymd') . '-' . strtoupper(Str::random(5));
        $weightKg = (float) ($data['parcel_weight'] ?? 1.00);
        $codAmount = (float) ($data['cod_amount'] ?? 0.00);
        $rateRule = $this->getActiveRateRule('motorcycle');

        $originHub = $data['origin_hub'] ?? 'Kathmandu Central Hub, Sinamangal';
        $destHub = $data['destination_hub'] ?? ($data['destination_city'] . ' Regional Hub');

        // Leg 1: First-Mile Pickup (Seller -> Origin Hub)
        $leg1Fee = $rateRule->calculateFee(4.0, $weightKg, 0);
        $leg1 = ShipmentAssignment::create([
            'master_awb' => $masterAwb,
            'assignment_type' => 'pickup_first_mile',
            'provider_type' => 'rider',
            'sequence' => 1,
            'pickup_name' => $seller->business_name ?? $seller->name,
            'pickup_phone' => $seller->phone ?? '9800000000',
            'pickup_address' => $seller->address ?? 'Kathmandu',
            'delivery_name' => 'NETPACK Origin Sorting Gateway',
            'delivery_phone' => '01-4112233',
            'delivery_address' => $originHub,
            'distance_km' => 4.0,
            'parcel_weight' => $weightKg,
            'provider_fee' => $leg1Fee,
            'cod_amount' => 0.00,
            'status' => 'assigned',
            'current_custody' => 'Seller (' . ($seller->business_name ?? $seller->name) . ')',
            'pickup_otp' => ShipmentAssignment::generateOtp(),
            'delivery_otp' => ShipmentAssignment::generateOtp(),
            'assigned_at' => now(),
        ]);
        $this->broadcastOffers($leg1);

        // Leg 2: Linehaul Transport (Origin Hub -> Destination Hub via Domestic Partner)
        $partnerId = $partner ? $partner->id : DomesticPartner::where('is_active', true)->value('id');
        $leg2 = ShipmentAssignment::create([
            'master_awb' => $masterAwb,
            'assignment_type' => 'line_haul',
            'provider_type' => 'domestic_partner',
            'partner_id' => $partnerId,
            'sequence' => 2,
            'pickup_name' => 'NETPACK Origin Sorting Gateway',
            'pickup_phone' => '01-4112233',
            'pickup_address' => $originHub,
            'delivery_name' => 'NETPACK Destination Regional Gateway',
            'delivery_phone' => '061-520000',
            'delivery_address' => $destHub,
            'distance_km' => (float) ($data['intercity_distance_km'] ?? 200.0),
            'parcel_weight' => $weightKg,
            'provider_fee' => 150.00, // Partner linehaul contract rate
            'cod_amount' => 0.00,
            'status' => 'assigned',
            'current_custody' => 'Origin Hub Sortation',
            'pickup_otp' => ShipmentAssignment::generateOtp(),
            'delivery_otp' => ShipmentAssignment::generateOtp(),
            'assigned_at' => now(),
        ]);

        // Leg 3: Last-Mile Delivery (Destination Hub -> Customer with COD)
        $leg3Fee = $rateRule->calculateFee(5.0, $weightKg, $codAmount);
        $leg3 = ShipmentAssignment::create([
            'master_awb' => $masterAwb,
            'assignment_type' => 'last_mile',
            'provider_type' => 'rider',
            'sequence' => 3,
            'pickup_name' => 'NETPACK Destination Regional Gateway',
            'pickup_phone' => '061-520000',
            'pickup_address' => $destHub,
            'delivery_name' => $data['delivery_name'],
            'delivery_phone' => $data['delivery_phone'],
            'delivery_address' => $data['delivery_address'],
            'distance_km' => 5.0,
            'parcel_weight' => $weightKg,
            'provider_fee' => $leg3Fee,
            'cod_amount' => $codAmount,
            'status' => 'assigned',
            'current_custody' => 'Awaiting Linehaul Arrival',
            'pickup_otp' => ShipmentAssignment::generateOtp(),
            'delivery_otp' => ShipmentAssignment::generateOtp(),
            'assigned_at' => now(),
        ]);

        return [
            'master_awb' => $masterAwb,
            'leg_1' => $leg1,
            'leg_2' => $leg2,
            'leg_3' => $leg3,
        ];
    }

    /**
     * Broadcast job offers to eligible verified riders
     */
    public function broadcastOffers(ShipmentAssignment $assignment): int
    {
        // Find verified riders who can accept the COD amount
        $riders = RiderProfile::where('verification_status', 'verified')
            ->get()
            ->filter(function (RiderProfile $rider) use ($assignment) {
                return $rider->canAcceptCod($assignment->cod_amount);
            });

        $count = 0;
        foreach ($riders as $rider) {
            RiderJobOffer::create([
                'assignment_id' => $assignment->id,
                'rider_profile_id' => $rider->id,
                'offered_amount' => $assignment->provider_fee,
                'offered_at' => now(),
                'expires_at' => now()->addMinutes(30),
                'response' => 'pending',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Accept a job offer by a rider
     */
    public function acceptJob(RiderProfile $rider, int $assignmentId): array
    {
        $assignment = ShipmentAssignment::findOrFail($assignmentId);

        if ($assignment->rider_profile_id && $assignment->rider_profile_id !== $rider->id) {
            return ['success' => false, 'message' => 'This delivery job has already been claimed by another rider.'];
        }

        if (!$rider->canAcceptCod($assignment->cod_amount)) {
            return ['success' => false, 'message' => "Your current COD limit of Rs. {$rider->cod_limit} has been reached. Please deposit outstanding cash first."];
        }

        $assignment->update([
            'rider_profile_id' => $rider->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'current_custody' => "Rider Assigned: {$rider->full_name} ({$rider->rider_code})",
        ]);

        // Update the accepted offer and mark others expired
        RiderJobOffer::where('assignment_id', $assignmentId)
            ->where('rider_profile_id', $rider->id)
            ->update(['response' => 'accepted', 'responded_at' => now()]);

        RiderJobOffer::where('assignment_id', $assignmentId)
            ->where('rider_profile_id', '!=', $rider->id)
            ->update(['response' => 'expired']);

        return [
            'success' => true,
            'message' => 'Job successfully accepted! Please navigate to pickup address.',
            'assignment' => $assignment,
        ];
    }

    /**
     * Rider arrives at pickup location
     */
    public function arriveAtPickup(ShipmentAssignment $assignment): bool
    {
        return $assignment->update([
            'status' => 'arrived_pickup',
            'arrived_pickup_at' => now(),
        ]);
    }

    /**
     * Verify pickup OTP and transfer custody to Rider
     */
    public function verifyPickupOtp(ShipmentAssignment $assignment, string $otp): array
    {
        if ($assignment->verifyPickupOtp($otp)) {
            return [
                'success' => true,
                'message' => 'Pickup OTP verified! Parcel is now in your custody. Proceed to delivery.',
                'assignment' => $assignment->fresh(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Pickup OTP. Please ask the seller to provide the correct 6-digit OTP.',
        ];
    }

    /**
     * Complete delivery with Customer OTP & COD collection
     */
    public function completeDelivery(
        ShipmentAssignment $assignment,
        string $otp,
        ?string $recipientName = null,
        ?string $podPhoto = null
    ): array {
        if ($assignment->completeDelivery($otp, $recipientName, $podPhoto)) {
            return [
                'success' => true,
                'message' => 'Delivery successfully verified and completed! Earnings credited.',
                'assignment' => $assignment->fresh(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Delivery OTP. Please verify the 6-digit OTP with the customer.',
        ];
    }

    /**
     * Record delivery failure / exception
     */
    public function recordFailure(ShipmentAssignment $assignment, string $reason, ?string $notes = null, ?string $photo = null): bool
    {
        return $assignment->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'failure_notes' => $notes,
            'failure_photo_path' => $photo,
            'failed_at' => now(),
            'current_custody' => "Delivery Failed: {$reason} - In Return Transit",
        ]);
    }
}
