<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test public services catalog endpoint
     */
    public function test_can_list_services(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'category', 'price_per_unit', 'unit']
                ]
            ]);
    }

    /**
     * Test user login and token generation
     */
    public function test_user_can_login(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'budi@gmail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => ['user', 'token']
            ]);
    }

    /**
     * Test real-time order tracking endpoint
     */
    public function test_can_track_order_timeline(): void
    {
        $order = Order::first();

        $response = $this->getJson('/api/v1/orders/' . $order->id . '/track');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_code' => $order->order_code,
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'order_code',
                    'current_status',
                    'status_label',
                    'current_step',
                    'timeline' => [
                        '*' => ['step', 'status_key', 'title', 'is_completed', 'is_current']
                    ]
                ]
            ]);
    }

    /**
     * Test order status lifecycle transition
     */
    public function test_can_update_order_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test_admin')->plainTextToken;
        $order = Order::first();

        $response = $this->patchJson('/api/v1/orders/' . $order->id . '/status', [
            'status' => 'ironing',
            'notes'  => 'Disetrika uap oleh admin pengujian',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'ironing',
        ]);

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'status'   => 'ironing',
        ]);
    }
}
