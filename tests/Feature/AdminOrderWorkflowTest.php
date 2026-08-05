<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminOrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function order(): Order
    {
        return Order::create(['order_number' => 'EES-WORKFLOW', 'customer_name' => 'Customer', 'email' => 'customer@example.com', 'phone' => '01700000000', 'shipping_address' => 'Dhaka', 'total_amount' => '1000', 'subtotal_amount' => '1000', 'shipping_charge' => '0', 'payment_method' => 'COD', 'payment_status' => 'pending', 'order_status' => 'awaiting']);
    }

    public function test_valid_transitions_are_historicized_and_notified(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->order();
        $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order), ['to_status' => 'processing'])->assertSessionHasNoErrors();
        $order->refresh();
        $this->assertSame('processing', $order->order_status);
        $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order), ['to_status' => 'shipped'])->assertSessionHasNoErrors();
        $order->refresh();
        $this->assertSame('shipped', $order->order_status);
        $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order), ['to_status' => 'delivered', 'shipping_provider' => 'Pathao', 'tracking_number' => 'TRK-123', 'tracking_url' => 'example.com/track/TRK-123'])->assertSessionHasNoErrors();
        $order->refresh();
        $this->assertSame('https://example.com/track/TRK-123', $order->tracking_url);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'from_status' => 'processing', 'to_status' => 'shipped', 'changed_by_user_id' => $admin->id]);
        Notification::assertSentOnDemand(OrderStatusUpdated::class);
    }

    public function test_statuses_can_move_anywhere_but_same_status_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->order();
        $order->update(['order_status' => 'delivered']);
        $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order), ['to_status' => 'processing'])->assertSessionHasNoErrors();
        $this->assertSame('processing', $order->fresh()->order_status);
        $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order), ['to_status' => 'processing'])->assertSessionHasErrors('status');
        $this->assertSame('processing', $order->fresh()->order_status);
    }
}
