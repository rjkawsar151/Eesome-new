<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_payment_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'order_number' => 'EES-TEST-PAY',
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '01700000000',
            'shipping_address' => 'Test address',
            'total_amount' => '1000.00',
            'subtotal_amount' => '1000.00',
            'shipping_charge' => '0.00',
            'payment_method' => 'COD',
            'payment_status' => 'unpaid',
            'order_status' => 'awaiting',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.updatePayment', $order), [
            'payment_status' => 'paid',
            'transaction_id' => 'TRX999888',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('TRX999888', $order->transaction_id);
    }
}
