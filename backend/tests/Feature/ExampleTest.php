<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test root redirect to customer portal.
     */
    public function test_root_redirects_to_portal(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect(route('customer.index'));
    }

    /**
     * Test customer portal loads successfully.
     */
    public function test_customer_portal_loads(): void
    {
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('LaundryHub');
        $response->assertSee('Customer Web Portal');
    }

    /**
     * Test customer order placement via web portal.
     */
    public function test_customer_can_place_order_via_portal(): void
    {
        $service = Service::first();

        $payload = [
            'service_id'       => $service->id,
            'delivery_type'    => 'pickup_delivery',
            'estimated_weight' => 3.5,
            'customer_name'    => 'Andi Wijaya',
            'customer_phone'   => '081298765432',
            'pickup_address'   => 'Jl. Pahlawan No. 12, Jakarta',
            'payment_method'   => 'qris',
            'customer_notes'   => 'Jangan pakai pewangi mawar',
        ];

        $response = $this->post('/portal/pesan', $payload);
        $response->assertStatus(302); // Redirect to tracking page
        
        $this->assertDatabaseHas('orders', [
            'total_weight'   => 3.5,
            'payment_method' => 'qris',
            'delivery_type'  => 'pickup_delivery',
        ]);
    }

    /**
     * Test admin dashboard page loads successfully.
     */
    public function test_admin_dashboard_loads(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('LaundryHub');
    }
}
