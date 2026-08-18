<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleOrder(string $status = 'confirmed'): Order
    {
        return Order::create([
            'order_number' => 'EES-TRACK-100',
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '01700000000',
            'shipping_address' => '123 Luxury Lane, Dhaka',
            'shipping_method' => 'Express Courier',
            'total_amount' => '1500.00',
            'subtotal_amount' => '1500.00',
            'shipping_charge' => '60.00',
            'payment_method' => 'COD',
            'payment_status' => 'pending',
            'order_status' => $status,
        ]);
    }

    public function test_guest_can_access_track_order_page(): void
    {
        $response = $this->get(route('orders.track'));
        $response->assertStatus(200);
        $response->assertSee('Track Your Order');
    }

    public function test_signed_email_token_loads_order_without_login(): void
    {
        $order = $this->createSampleOrder('shipped');
        $token = hash_hmac('sha256', $order->id . '|' . $order->order_number, config('app.key'));
        
        $signedUrl = URL::signedRoute('orders.track', [
            'order' => $order->id,
            'token' => $token,
        ]);

        $response = $this->get($signedUrl);
        $response->assertStatus(200);
        $response->assertSee('Order #' . $order->order_number);
        $response->assertSee('Shipped');
    }

    public function test_manual_order_number_search_finds_order(): void
    {
        $order = $this->createSampleOrder('processing');

        $response = $this->post(route('orders.track.search'), [
            'order_number' => 'EES-TRACK-100',
            'email_or_phone' => 'jane@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Order #' . $order->order_number);
        $response->assertSee('Processing');
    }

    public function test_invalid_order_number_returns_friendly_error(): void
    {
        $response = $this->post(route('orders.track.search'), [
            'order_number' => 'EES-NONEXISTENT',
        ]);

        $response->assertStatus(200);
        $response->assertSee("We couldn't find that order.");
    }

    public function test_cancellation_box_renders_only_for_cancelled_orders(): void
    {
        $orderConfirmed = $this->createSampleOrder('confirmed');
        $notifConfirmed = new OrderStatusUpdated($orderConfirmed->id);
        $mailConfirmed = $notifConfirmed->toMail($orderConfirmed);
        $htmlConfirmed = $mailConfirmed->render();

        $this->assertStringNotContainsString("We're sorry — your order has been cancelled", $htmlConfirmed);

        $orderCancelled = $this->createSampleOrder('cancelled');
        $notifCancelled = new OrderStatusUpdated($orderCancelled->id);
        $mailCancelled = $notifCancelled->toMail($orderCancelled);
        $htmlCancelled = $mailCancelled->render();

        $this->assertStringContainsString("We're sorry — your order has been cancelled", $htmlCancelled);
    }
}
