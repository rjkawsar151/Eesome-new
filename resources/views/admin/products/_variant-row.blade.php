@php
    $isOld = $isOld ?? false;
    $variantModel = $variantModel ?? null;
    $variantData = $variantData ?? [];
    $id = $isOld ? ($variantData['id'] ?? null) : ($variantModel?->id ?? null);
    $colorName = $isOld ? ($variantData['color_name'] ?? '') : ($variantModel?->color_name ?? '');
    $colorCode = $isOld ? ($variantData['color_code'] ?? '#be185d') : ($variantModel?->color_code ?? '#be185d');
    $sku = $isOld ? ($variantData['sku'] ?? '') : ($variantModel?->sku ?? '');
    $regularPrice = $isOld ? ($variantData['regular_price'] ?? '') : ($variantModel?->regular_price ?? '');
    $salePrice = $isOld ? ($variantData['sale_price'] ?? '') : ($variantModel?->sale_price ?? '');
    $stock = $isOld ? ($variantData['stock'] ?? '0') : ($variantModel?->stock ?? '0');
    $sortOrder = $isOld ? ($variantData['sort_order'] ?? $index) : ($variantModel?->sort_order ?? $index);
    $isDefault = $isOld ? (!empty($variantData['is_default'])) : ($variantModel?->is_default ?? ($index === 0));
    $isActive = $isOld ? (!empty($variantData['is_active'])) : ($variantModel?->is_active ?? true);
    $currentImage = !$isOld && !empty($variantModel?->image) ? app(\App\Services\ProductImageResolver::class)->resolve($variantModel->image) : null;
@endphp

<div class="variant-card" data-index="{{ $index }}">
    @if($id)
        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $id }}">
    @endif
    <div class="variant-card-head">
        <div class="variant-card-title">
            <span class="var-swatch-preview" style="background-color: {{ $colorCode ?: '#be185d' }}"></span>
            <strong>Variant #<span class="var-num">{{ $index + 1 }}</span></strong>
            <span class="badge var-color-badge">{{ $colorName ?: 'Untitled' }}</span>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-remove-row">Remove</button>
    </div>
    <div class="var-grid">
        <div class="field">
            <label>Color name<span class="req">*</span></label>
            <input class="input var-color-input" type="text" name="variants[{{ $index }}][color_name]" value="{{ $colorName }}" required placeholder="e.g. Rose Pink">
        </div>
        <div class="field">
            <label>Color swatch<span class="req">*</span></label>
            <div class="swatch-wrap">
                <input class="input swatch var-swatch-input" type="color" name="variants[{{ $index }}][color_code]" value="{{ $colorCode ?: '#be185d' }}" required>
                <input class="input var-hex-input" type="text" value="{{ $colorCode ?: '#be185d' }}" placeholder="#be185d" style="width:90px;font-family:monospace;font-size:.85rem;padding:.4rem .5rem">
            </div>
        </div>
        <div class="field">
            <label>Variant SKU<span class="req">*</span></label>
            <input class="input var-sku-input" type="text" name="variants[{{ $index }}][sku]" value="{{ $sku }}" required placeholder="e.g. BAG-ROSE">
        </div>
        <div class="field">
            <label>Regular price<span class="req">*</span></label>
            <input class="input var-price-input" type="number" step="0.01" min="0" name="variants[{{ $index }}][regular_price]" value="{{ $regularPrice }}" required>
        </div>
        <div class="field">
            <label>Sale price</label>
            <input class="input" type="number" step="0.01" min="0" name="variants[{{ $index }}][sale_price]" value="{{ $salePrice }}" placeholder="Optional">
        </div>
        <div class="field">
            <label>Stock<span class="req">*</span></label>
            <input class="input var-stock-input" type="number" min="0" name="variants[{{ $index }}][stock]" value="{{ $stock }}" required>
        </div>
        <div class="field">
            <label>Display order</label>
            <input class="input var-order-input" type="number" min="0" name="variants[{{ $index }}][sort_order]" value="{{ $sortOrder }}">
        </div>
        <div class="field">
            <label>Variant image</label>
            <input class="input" type="file" name="variants[{{ $index }}][image]" accept="image/png,image/webp,image/jpeg">
            @if($currentImage)
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.35rem">
                    <img src="{{ $currentImage }}" alt="" style="width:36px;height:36px;object-fit:contain;border-radius:4px;border:1px solid #ddd">
                    <small class="subtle">Current image</small>
                </div>
            @endif
        </div>
        <div class="full variant-toggles">
            <label class="check-label"><input type="checkbox" name="variants[{{ $index }}][is_default]" value="1" @checked($isDefault)> Default color</label>
            <label class="check-label"><input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" @checked($isActive)> Enabled</label>
        </div>
    </div>
</div>
