<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_tracking_is_privacy_safe(): void
    {
        $shipment = $this->createShipment();

        $this->get(route('tracking.show', $shipment->tracking_number))
            ->assertOk()
            ->assertSee('NPI-2026-000101-7')
            ->assertSee('In Transit')
            ->assertSee('Kathmandu, Nepal')
            ->assertSee('New York, United States')
            ->assertDontSee('9800000000')
            ->assertDontSee('555-0100')
            ->assertDontSee('Preview Street')
            ->assertDontSee('$92.00')
            ->assertDontSee('View HAWB');

        $this->actingAs($shipment->customer)
            ->get(route('shipments.show', $shipment))
            ->assertOk()
            ->assertSee('NPI-2026-000101-7')
            ->assertDontSee('9800000000');
    }

    public function test_admin_can_open_shipment_list_and_private_details(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'super_admin',
            'verification_status' => 'approved',
            'registration_completed' => true,
            'password_changed' => true,
        ]);
        $shipment = $this->createShipment();

        $this->actingAs($admin)
            ->get(route('admin.shipments.index'))
            ->assertOk()
            ->assertSee($shipment->tracking_number);

        $this->actingAs($admin)
            ->get(route('admin.shipments.show', $shipment->id))
            ->assertOk()
            ->assertSee('9800000000')
            ->assertSee('Preview Street')
            ->assertSee('Update Tracking');
    }

    private function createShipment(): Shipment
    {
        $customer = User::factory()->create([
            'user_type' => 'customer',
            'verification_status' => 'approved',
            'registration_completed' => true,
        ]);

        return Shipment::create([
            'hawb_number' => 'USNP-2026-101',
            'tracking_number' => 'NPI-2026-000101-7',
            'customer_id' => $customer->id,
            'sender_name' => 'NETPACK Kathmandu',
            'sender_phone' => '9800000000',
            'sender_address' => 'Thamel',
            'sender_city' => 'Kathmandu',
            'sender_country' => 'Nepal',
            'receiver_name' => 'Preview Customer',
            'receiver_phone' => '555-0100',
            'receiver_address' => 'Preview Street',
            'receiver_city' => 'New York',
            'receiver_country' => 'United States',
            'service_type' => 'express',
            'shipment_type' => 'parcel',
            'actual_weight' => 2.4,
            'chargeable_weight' => 3,
            'shipping_cost' => 85,
            'total_amount' => 92,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'status' => 'in_transit',
            'tracking_history' => [[
                'status' => 'in_transit',
                'status_label' => 'In Transit',
                'description' => 'Departed transit facility',
                'location' => 'Dubai Transit Hub',
                'time' => now()->toIso8601String(),
            ]],
        ]);
    }
}
