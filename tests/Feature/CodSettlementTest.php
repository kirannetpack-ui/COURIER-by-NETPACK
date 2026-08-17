<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\RiderDeposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodSettlementTest extends TestCase
{
    use RefreshDatabase;

    public function test_settlement_finalizes_existing_hold_without_deducting_rider_twice(): void
    {
        User::factory()->create(['user_type' => User::TYPE_SUPER_ADMIN]);
        $seller = User::factory()->create(['user_type' => User::TYPE_SELLER]);
        $rider = User::factory()->create([
            'user_type' => User::TYPE_RIDER,
            'rider_deposit_balance' => 400,
            'rider_delivery_fee' => 100,
            'rider_commission_rate' => 10,
            'rider_margin_rate' => 15,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-COD-001',
            'seller_id' => $seller->id,
            'rider_id' => $rider->id,
            'customer_name' => 'COD Customer',
            'customer_phone' => '9800000000',
            'shipping_address' => 'Kathmandu',
            'total_amount' => 100,
            'cod_amount' => 100,
            'payment_method' => 'cod',
            'status' => 'out_for_delivery',
            'tracking_number' => 'NPE-2026-000002-4',
        ]);
        $hold = RiderDeposit::create([
            'rider_id' => $rider->id,
            'amount' => -100,
            'balance' => 400,
            'type' => 'settlement',
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'description' => 'COD deposit hold',
            'status' => 'pending',
        ]);

        $this->actingAs($rider)
            ->post(route('rider.cod.settle', $order), ['cod_collected_amount' => 100])
            ->assertRedirect(route('rider.orders.my'));

        $this->assertSame('400.00', $rider->fresh()->rider_deposit_balance);
        $this->assertSame('completed', $hold->fresh()->status);
        $this->assertSame('delivered', $order->fresh()->status);
    }
}
