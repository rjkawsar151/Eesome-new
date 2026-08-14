<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\MetaCapiService;
use Illuminate\Http\Request;
use Tests\TestCase;

class MetaCapiServiceTest extends TestCase
{
    public function test_catalogue_content_id_matches_sku_or_id(): void
    {
        $service = new MetaCapiService();

        $productWithSku = new Product(['id' => 101, 'sku' => 'BAG-001', 'name' => 'Leather Tote']);
        $productWithSku->id = 101;
        $this->assertEquals('BAG-001', $service->getCatalogueContentId($productWithSku));

        $productWithoutSku = new Product(['id' => 102, 'sku' => null, 'name' => 'Canvas Tote']);
        $productWithoutSku->id = 102;
        $this->assertEquals('102', $service->getCatalogueContentId($productWithoutSku));

        $variant = new ProductVariant(['id' => 501, 'sku' => 'BAG-001-BLK', 'color_name' => 'Black']);
        $variant->id = 501;
        $this->assertEquals('BAG-001-BLK', $service->getCatalogueContentId($productWithSku, $variant));
    }

    public function test_build_user_data_formats_phone_and_names(): void
    {
        $service = new MetaCapiService();
        $request = Request::create('https://eesomebd.store/checkout', 'POST', [], [
            '_fbp' => 'fb.1.1680000000.123456789',
            '_fbc' => 'fb.1.1680000000.IwAR2xyz',
        ], [], [
            'REMOTE_ADDR' => '103.100.100.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);

        $userData = $service->buildUserData($request, [
            'name' => 'Rahim Chowdhury',
            'email' => 'RAHIM@GMAIL.COM',
            'phone' => '01712-345678',
            'district' => 'Dhaka',
            'thana' => 'Dhanmondi',
            'post_code' => '1205',
        ]);

        $this->assertNotNull($userData);
        $this->assertEquals('103.100.100.1', $userData->getClientIpAddress());
        $this->assertEquals('Mozilla/5.0 (Windows NT 10.0; Win64; x64)', $userData->getClientUserAgent());
        $this->assertEquals('fb.1.1680000000.123456789', $userData->getFbp());
        $this->assertEquals('fb.1.1680000000.IwAR2xyz', $userData->getFbc());
    }

    public function test_view_content_and_add_to_cart_return_event_id(): void
    {
        $service = new MetaCapiService();
        $product = new Product([
            'id' => 1,
            'name' => 'Signature Bag',
            'sku' => 'SIG-001',
            'price' => '2500',
            'is_active' => true,
        ]);
        $product->id = 1;

        $eventId = 'test_event_123';
        $returnedEventId = $service->trackViewContent($product, null, $eventId);
        $this->assertEquals('test_event_123', $returnedEventId);

        $atcEventId = $service->trackAddToCart($product, 2, 5000, null, 'atc_test_456');
        $this->assertEquals('atc_test_456', $atcEventId);
    }
}
