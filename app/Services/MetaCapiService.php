<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use FacebookAds\Api;
use FacebookAds\Logger\NullLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaCapiService
{
    protected ?string $pixelId;
    protected ?string $accessToken;
    protected ?string $testEventCode;

    public function __construct(?SiteSettingsRepository $settings = null)
    {
        $settings = $settings ?? app(SiteSettingsRepository::class);

        $this->pixelId = (string) ($settings->get('meta_pixel_id') ?: (config('tracking.meta.pixel_id') ?: config('services.meta.pixel_id')));
        $this->accessToken = (string) ($settings->get('meta_capi_token') ?: (config('tracking.meta.capi_token') ?: config('services.meta.capi_token')));
        $this->testEventCode = ($settings->get('meta_test_event_code') ?: (config('tracking.meta.test_event_code') ?: config('services.meta.test_event_code'))) ?: null;

        if ($this->accessToken) {
            Api::init(null, null, $this->accessToken);
            if (Api::instance()) {
                Api::instance()->setLogger(new NullLogger());
            }
        }
    }

    /**
     * Build UserData payload for Meta CAPI with advanced matching.
     */
    public function buildUserData(
        ?Request $request = null,
        ?array $customerData = null,
        ?User $user = null
    ): UserData {
        $request = $request ?? request();
        $userData = new UserData();

        // 1. Client IP & User Agent
        $ip = $request?->ip();
        if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
            $userData->setClientIpAddress($ip);
        }

        $userAgent = $request?->userAgent();
        if ($userAgent) {
            $userData->setClientUserAgent($userAgent);
        }

        // 2. FBP and FBC Cookies
        $fbp = $request?->cookie('_fbp');
        if ($fbp) {
            $userData->setFbp($fbp);
        }

        $fbc = $request?->cookie('_fbc');
        if (! $fbc && $request?->has('fbclid')) {
            $fbclid = $request->query('fbclid');
            $fbc = 'fb.1.' . time() . '.' . $fbclid;
        }
        if ($fbc) {
            $userData->setFbc($fbc);
        }

        // 3. User identifiers from Auth / User model
        $authenticatedUser = $user ?? Auth::user();
        if ($authenticatedUser) {
            $userData->setExternalId((string) $authenticatedUser->id);
            if ($authenticatedUser->email) {
                $userData->setEmail(trim(strtolower($authenticatedUser->email)));
            }
            if (! empty($authenticatedUser->phone)) {
                $userData->setPhone($this->normalizePhone($authenticatedUser->phone));
            }
            if (! empty($authenticatedUser->name)) {
                $names = $this->splitName($authenticatedUser->name);
                if (! empty($names['first_name'])) {
                    $userData->setFirstName($names['first_name']);
                }
                if (! empty($names['last_name'])) {
                    $userData->setLastName($names['last_name']);
                }
            }
        }

        // 4. Override / Supplement with Customer Data from Checkout
        if ($customerData) {
            if (! empty($customerData['email'])) {
                $userData->setEmail(trim(strtolower($customerData['email'])));
            }
            if (! empty($customerData['phone'])) {
                $userData->setPhone($this->normalizePhone($customerData['phone']));
            }
            if (! empty($customerData['name'])) {
                $names = $this->splitName($customerData['name']);
                if (! empty($names['first_name'])) {
                    $userData->setFirstName($names['first_name']);
                }
                if (! empty($names['last_name'])) {
                    $userData->setLastName($names['last_name']);
                }
            }
            if (! empty($customerData['district'])) {
                $userData->setCity(trim(strtolower($customerData['district'])));
            }
            if (! empty($customerData['thana'])) {
                $userData->setState(trim(strtolower($customerData['thana'])));
            }
            if (! empty($customerData['post_code'])) {
                $userData->setZipCode(trim(strtolower($customerData['post_code'])));
            }
            $userData->setCountryCode('bd');
        }

        return $userData;
    }

    /**
     * Resolve exact catalogue content ID for a Product / Variant.
     * Matches the Meta Catalog Feed (<g:id>{{ $product->sku ?: $product->id }}</g:id>).
     */
    public function getCatalogueContentId(Product $product, ?ProductVariant $variant = null): string
    {
        if ($variant && ! empty($variant->sku)) {
            return (string) $variant->sku;
        }

        return ! empty($product->sku) ? (string) $product->sku : (string) $product->id;
    }

    /**
     * Track ViewContent (Product detail view).
     */
    public function trackViewContent(
        Product $product,
        ?Request $request = null,
        ?string $eventId = null
    ): ?string {
        $eventId = $eventId ?: (string) Str::uuid();
        $contentId = $this->getCatalogueContentId($product);
        $price = (float) $product->effective_price;

        $content = (new Content())
            ->setProductId($contentId)
            ->setQuantity(1)
            ->setItemPrice($price)
            ->setTitle($product->name);

        $customData = (new CustomData())
            ->setContentIds([$contentId])
            ->setContentType('product')
            ->setContentName($product->name)
            ->setContentCategory($product->category?->name ?? 'Handbags')
            ->setValue($price)
            ->setCurrency('BDT')
            ->setContents([$content]);

        $userData = $this->buildUserData($request);
        $sourceUrl = $request?->fullUrl() ?? route('products.show', $product->slug ?: $product->id);

        $this->sendEvent('ViewContent', $userData, $customData, $eventId, $sourceUrl);

        return $eventId;
    }

    /**
     * Track AddToCart.
     */
    public function trackAddToCart(
        Product $product,
        int $quantity = 1,
        ?float $value = null,
        ?Request $request = null,
        ?string $eventId = null,
        ?ProductVariant $variant = null
    ): ?string {
        $eventId = $eventId ?: (string) Str::uuid();
        $contentId = $this->getCatalogueContentId($product, $variant);
        $unitPrice = (float) ($variant ? $variant->effective_price : $product->effective_price);
        $totalValue = (float) ($value ?? ($unitPrice * $quantity));

        $content = (new Content())
            ->setProductId($contentId)
            ->setQuantity($quantity)
            ->setItemPrice($unitPrice)
            ->setTitle($product->name);

        $customData = (new CustomData())
            ->setContentIds([$contentId])
            ->setContentType('product')
            ->setContentName($product->name)
            ->setValue($totalValue)
            ->setCurrency('BDT')
            ->setContents([$content]);

        $userData = $this->buildUserData($request);
        $sourceUrl = $request?->fullUrl() ?? route('products.show', $product->slug ?: $product->id);

        $this->sendEvent('AddToCart', $userData, $customData, $eventId, $sourceUrl);

        return $eventId;
    }

    /**
     * Track Purchase (Checkout Complete).
     */
    public function trackPurchase(
        Order $order,
        ?Request $request = null,
        ?string $eventId = null
    ): ?string {
        $eventId = $eventId ?: ('order_' . $order->order_number);

        // Load items if not loaded
        if (! $order->relationLoaded('items')) {
            $order->load(['items.product']);
        }

        $contentIds = [];
        $contents = [];
        $totalItems = 0;

        foreach ($order->items as $item) {
            $contentId = ! empty($item->product_sku)
                ? (string) $item->product_sku
                : (! empty($item->product?->sku) ? (string) $item->product->sku : (string) $item->product_id);

            $contentIds[] = $contentId;
            $qty = (int) $item->quantity;
            $totalItems += $qty;
            $unitPrice = (float) $item->price;

            $contents[] = (new Content())
                ->setProductId($contentId)
                ->setQuantity($qty)
                ->setItemPrice($unitPrice)
                ->setTitle($item->product_name ?: 'Product');
        }

        $customerData = [
            'name' => $order->customer_name,
            'email' => $order->email,
            'phone' => $order->phone,
            'district' => $order->district,
            'thana' => $order->thana,
            'post_code' => $order->post_code,
        ];

        $userData = $this->buildUserData($request, $customerData, $order->user);

        $customData = (new CustomData())
            ->setContentIds(array_values(array_unique($contentIds)))
            ->setContentType('product')
            ->setValue((float) $order->total_amount)
            ->setCurrency('BDT')
            ->setOrderId((string) $order->order_number)
            ->setNumItems((string) $totalItems)
            ->setContents($contents);

        $sourceUrl = $request?->fullUrl() ?? route('checkout.success', $order->order_number);

        $this->sendEvent('Purchase', $userData, $customData, $eventId, $sourceUrl);

        return $eventId;
    }

    /**
     * Send event to Meta Graph API Conversions endpoint.
     */
    public function sendEvent(
        string $eventName,
        UserData $userData,
        CustomData $customData,
        string $eventId,
        ?string $sourceUrl = null
    ): bool {
        if (empty($this->pixelId) || empty($this->accessToken)) {
            Log::debug('Meta CAPI Skipped: Pixel ID or Access Token not configured.');
            return false;
        }

        try {
            $event = (new Event())
                ->setEventName($eventName)
                ->setEventTime(time())
                ->setEventSourceUrl($sourceUrl ?: url('/'))
                ->setActionSource(ActionSource::WEBSITE)
                ->setUserData($userData)
                ->setCustomData($customData)
                ->setEventId($eventId);

            $eventRequest = (new EventRequest($this->pixelId))
                ->setEvents([$event]);

            if ($this->testEventCode) {
                $eventRequest->setTestEventCode($this->testEventCode);
            }

            $response = $eventRequest->execute();

            Log::info("Meta CAPI Event Sent [{$eventName}]", [
                'event_id' => $eventId,
                'events_received' => $response->getEventsReceived(),
                'fbtrace_id' => $response->getFbtraceId(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("Meta CAPI Error [{$eventName}]: " . $e->getMessage(), [
                'event_id' => $eventId,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Normalize Bangladeshi and international phone numbers to E.164 without leading plus.
     * Meta requires digits only with country code (e.g. 8801712345678).
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (Str::startsWith($digits, '880')) {
            return $digits;
        }

        if (Str::startsWith($digits, '01') && strlen($digits) === 11) {
            return '88' . $digits;
        }

        return $digits;
    }

    /**
     * Split full name into first and last name for matching.
     */
    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name));
        if (empty($parts)) {
            return ['first_name' => '', 'last_name' => ''];
        }

        $firstName = array_shift($parts);
        $lastName = ! empty($parts) ? implode(' ', $parts) : '';

        return [
            'first_name' => strtolower($firstName),
            'last_name' => strtolower($lastName),
        ];
    }
}
