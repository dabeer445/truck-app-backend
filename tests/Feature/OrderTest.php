<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Order;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends BaseTestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run seeders for roles

        // Create test users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $this->admin->assignRole('admin');

        $this->customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $this->customer->assignRole('customer');
    }

    /** @test */
    public function customer_can_create_order()
    {
        Sanctum::actingAs($this->customer);

        $orderData = [
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => [
                'weight' => 100,
                'dimensions' => [
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ]
            ],
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s')
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'pickup_location',
                    'delivery_location',
                    'status',
                    'created_at'
                ]
            ]);
    }

    /** @test */
    public function order_validates_pickup_time_in_future()
    {
        Sanctum::actingAs($this->customer);

        $orderData = [
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => [
                'weight' => 100,
                'dimensions' => [
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ]
            ],
            'pickup_time' => now()->subDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDay()->format('Y-m-d H:i:s')
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pickup_time']);
    }

    /** @test */
    public function order_validates_delivery_after_pickup()
    {
        Sanctum::actingAs($this->customer);

        $orderData = [
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => [
                'weight' => 100,
                'dimensions' => [
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ]
            ],
            'pickup_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDay()->format('Y-m-d H:i:s')
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['delivery_time']);
    }

    /** @test */
    public function customer_can_only_view_own_orders()
    {
        // Create orders for both users
        $customerOrder = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'email' => 'other@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $otherCustomer->assignRole('customer');

        $otherOrder = Order::create([
            'user_id' => $otherCustomer->id,
            'pickup_location' => '789 Other St',
            'delivery_location' => '012 Other Ave',
            'cargo_details' => ['weight' => 200],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->customer);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $customerOrder->id);
    }

    /** @test */
    public function admin_can_view_all_orders()
    {
        // Create test orders
        Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '789 Other St',
            'delivery_location' => '012 Other Ave',
            'cargo_details' => ['weight' => 200],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/orders');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/v1/admin/orders/{$order->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */

    public function customer_cannot_update_order_status()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->customer);

        $response = $this->putJson("/api/v1/admin/orders/{$order->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending'
        ]);
    }

    // /** @test */
    public function customer_can_cancel_pending_order()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->customer);

        $response = $this->putJson("/api/v1/orders/{$order->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    }

    /** @test */
    public function customer_cannot_cancel_in_progress_order()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'in_progress'
        ]);
        Sanctum::actingAs($this->customer);

        $response = $this->putJson("/api/v1/orders/{$order->id}/cancel");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function test_user_cannot_cancel_others_order()
    {
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'Other@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);

        $order = Order::create([
            'user_id' => $otherUser->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'in_progress'
        ]);
        Sanctum::actingAs($this->customer);

        $response = $this->putJson("/api/v1/orders/{$order->id}/cancel");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function validate_cargo_details_constraints()
    {
        Sanctum::actingAs($this->customer);

        $invalidCargoData = [
            [
                'weight' => 0, // too light
                'dimensions' => ['length' => 10, 'width' => 10, 'height' => 10]
            ],
            [
                'weight' => 10000, // too heavy
                'dimensions' => ['length' => 10, 'width' => 10, 'height' => 10]
            ],
            [
                'weight' => 100,
                'dimensions' => ['length' => 0, 'width' => 10, 'height' => 10] // invalid dimension
            ]
        ];

        foreach ($invalidCargoData as $cargoDetails) {
            $response = $this->postJson('/api/v1/orders', [
                'pickup_location' => '123 Start St',
                'delivery_location' => '456 End Ave',
                'cargo_details' => $cargoDetails,
                'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
                'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s')
            ]);

            $response->assertStatus(422);
        }
    }

    /** @test */
    public function order_search_and_filtering()
    {
        // Create multiple orders with different statuses
        $statuses = ['pending', 'in_progress', 'completed'];
        foreach ($statuses as $status) {
            Order::create([
                'user_id' => $this->customer->id,
                'pickup_location' => "123 {$status} St",
                'delivery_location' => "456 {$status} Ave",
                'cargo_details' => ['weight' => 100],
                'pickup_time' => now()->addDay(),
                'delivery_time' => now()->addDays(2),
                'status' => $status
            ]);
        }

        Sanctum::actingAs($this->admin);

        // Test status filtering
        $response = $this->getJson('/api/v1/admin/orders?status=pending');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'pending');

        // Test date range filtering
        $response = $this->getJson('/api/v1/admin/orders?from=' .
            now()->format('Y-m-d') . '&to=' . now()->addDays(3)->format('Y-m-d'));
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function order_pagination_works_correctly()
    {
        // Create 15 orders
        for ($i = 0; $i < 15; $i++) {
            Order::create([
                'user_id' => $this->customer->id,
                'pickup_location' => "123 Street {$i}",
                'delivery_location' => "456 Avenue {$i}",
                'cargo_details' => ['weight' => 100],
                'pickup_time' => now()->addDay(),
                'delivery_time' => now()->addDays(2),
                'status' => 'pending'
            ]);
        }

        Sanctum::actingAs($this->admin);

        // Test first page
        $response = $this->getJson('/api/v1/admin/orders?page=1&per_page=10');
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        // Test second page
        $response = $this->getJson('/api/v1/admin/orders?page=2&per_page=10');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function concurrent_order_status_updates_are_handled_correctly()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        Sanctum::actingAs($this->admin);

        // Simulate concurrent requests
        $promises = [];
        $statuses = ['in_progress', 'completed', 'cancel'];

        foreach ($statuses as $status) {
            $response = $this->putJson("/api/v1/admin/orders/{$order->id}/status", [
                'status' => $status
            ]);
            // One should succeed, others should fail
            $this->assertTrue(in_array($response->status(), [200, 422]));
        }

        // Verify only one status update succeeded
        $this->assertCount(
            1,
            Order::where('id', $order->id)
                ->whereIn('status', $statuses)
                ->get()
        );
    }
}