<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class EcommerceTestSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🛒 Seeding E-commerce test data...');

        // Clear existing data (optional)
        // OrderItem::truncate();
        // Order::truncate();
        // Product::truncate();

        // 1. Create seller
        $seller = User::firstOrCreate(
            ['email' => 'seller@test.com'],
            [
                'name' => 'Test Seller',
                'password' => bcrypt('password123'),
                'user_type' => 'seller',
                'verification_status' => 'approved',
                'phone' => '9800000000',
                'registration_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->command->info('✅ Seller created: ' . $seller->email);

        // 2. Create rider
        $rider = User::firstOrCreate(
            ['email' => 'rider@test.com'],
            [
                'name' => 'Test Rider',
                'password' => bcrypt('password123'),
                'user_type' => 'rider',
                'verification_status' => 'approved',
                'phone' => '9800000001',
                'is_online' => true,
                'is_available' => true,
                'vehicle_type' => 'scooter',
                'registration_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->command->info('✅ Rider created: ' . $rider->email);

        // 3. Create products
        $productData = [
            ['name' => 'Nepali Rice (10kg)', 'price' => 1200, 'category' => 'Groceries', 'stock' => 50],
            ['name' => 'Cooking Oil (5L)', 'price' => 800, 'category' => 'Groceries', 'stock' => 30],
            ['name' => 'Salt (1kg)', 'price' => 150, 'category' => 'Groceries', 'stock' => 100],
            ['name' => 'Sugar (1kg)', 'price' => 200, 'category' => 'Groceries', 'stock' => 80],
            ['name' => 'Tea (100g)', 'price' => 300, 'category' => 'Beverages', 'stock' => 40],
            ['name' => 'Coffee (200g)', 'price' => 500, 'category' => 'Beverages', 'stock' => 35],
            ['name' => 'Noodles (1 pack)', 'price' => 80, 'category' => 'Snacks', 'stock' => 200],
            ['name' => 'Biscuits (200g)', 'price' => 120, 'category' => 'Snacks', 'stock' => 150],
            ['name' => 'Cooking Gas (1 cylinder)', 'price' => 1500, 'category' => 'Utilities', 'stock' => 20],
            ['name' => 'Drinking Water (20L)', 'price' => 80, 'category' => 'Beverages', 'stock' => 60],
        ];

        $createdProducts = [];
        foreach ($productData as $data) {
            $product = Product::create([
                'user_id' => $seller->id,
                'name' => $data['name'],
                'price_npr' => $data['price'],
                'category' => $data['category'],
                'sku' => 'SKU-' . strtoupper(substr(str_replace(' ', '', $data['name']), 0, 4)) . '-' . rand(100, 999),
                'stock_quantity' => $data['stock'],
                'is_active' => true,
                'description' => 'High quality ' . $data['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $createdProducts[] = $product;
            $this->command->info('✅ Product created: ' . $product->name);
        }

        $this->command->info('✅ Total products created: ' . count($createdProducts));

        // 4. Create test orders with different statuses
        $statuses = ['pending', 'assigned', 'picked_up', 'out_for_delivery', 'delivered', 'cancelled'];
        $customers = [
            ['name' => 'Ram Sharma', 'phone' => '9800000101'],
            ['name' => 'Sita Gurung', 'phone' => '9800000102'],
            ['name' => 'Hari Rana', 'phone' => '9800000103'],
            ['name' => 'Gita Thapa', 'phone' => '9800000104'],
            ['name' => 'Bikash Pandey', 'phone' => '9800000105'],
        ];

        $ordersCreated = 0;
        
        foreach ($statuses as $index => $status) {
            $customer = $customers[$index % count($customers)];
            $orderItemsCount = rand(1, 3);
            
            // Calculate totals
            $subtotal = 0;
            $items = [];
            
            for ($i = 0; $i < $orderItemsCount; $i++) {
                $product = $createdProducts[array_rand($createdProducts)];
                $quantity = rand(1, 3);
                $price = $product->price_npr;
                $subtotal += $price * $quantity;
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
            
            $tax = $subtotal * 0.13;
            $shippingCost = rand(50, 150);
            $total = $subtotal + $tax + $shippingCost;
            
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'seller_id' => $seller->id,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'shipping_address' => 'Kathmandu, Nepal - ' . ($index + 1),
                'total_amount' => $total,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'status' => $status,
                'payment_status' => $status === 'delivered' ? 'paid' : 'pending',
                'order_date' => now()->subHours(rand(1, 48)),
                'tracking_number' => Order::generateTrackingNumber(),
                'created_at' => now()->subHours(rand(1, 48)),
                'updated_at' => now(),
            ]);

            // Add order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // If assigned or beyond, assign rider
            if (in_array($status, ['assigned', 'picked_up', 'out_for_delivery', 'delivered'])) {
                $order->update([
                    'rider_id' => $rider->id,
                    'rider_assigned_at' => now()->subHours(rand(1, 12)),
                    'rider_acceptance_time' => now()->subHours(rand(1, 12)),
                ]);
            }

            // Set specific timestamps based on status
            if ($status === 'picked_up' || $status === 'out_for_delivery' || $status === 'delivered') {
                $order->update(['picked_up_at' => now()->subHours(rand(1, 6))]);
            }
            if ($status === 'out_for_delivery' || $status === 'delivered') {
                $order->update(['out_for_delivery_at' => now()->subHours(rand(1, 3))]);
            }
            if ($status === 'delivered') {
                $order->update([
                    'delivered_at' => now()->subHours(rand(1, 2)),
                    'payment_status' => 'paid',
                ]);
            }
            if ($status === 'cancelled') {
                $order->update([
                    'payment_status' => 'refunded',
                ]);
            }

            $ordersCreated++;
            $this->command->info("✅ Order #{$order->order_number} created with status: {$status} (Items: " . count($items) . ")");
        }

        $this->command->info('🎉 E-commerce test data seeded successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Sellers: 1');
        $this->command->info('   - Riders: 1');
        $this->command->info('   - Products: ' . count($createdProducts));
        $this->command->info('   - Orders: ' . $ordersCreated);
        
        // Show order status distribution
        $statusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        $this->command->info('   - Order Status Distribution:');
        foreach ($statusCounts as $statusCount) {
            $this->command->info('      • ' . $statusCount->status . ': ' . $statusCount->count);
        }
    }
}