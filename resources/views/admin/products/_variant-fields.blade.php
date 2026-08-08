<div class="variant-fields">
    <div class="field">
        <label>Color name<span class="req">*</span></label>
        <input class="input" type="text" name="color_name" value="{{ old('color_name',$variant->color_name) }}" required placeholder="e.g. Rose Pink">
    </div>
    <div class="field">
        <label>Color swatch<span class="req">*</span></label>
        <div class="swatch-wrap">
            <input class="input swatch" type="color" name="color_code" value="{{ old('color_code',$variant->color_code ?: '#be185d') }}" required aria-label="Pick a color">
            <span class="subtle" style="font-size:.8rem">Pick a color</span>
        </div>
    </div>
    <div class="field">
        <label>Variant SKU<span class="req">*</span></label>
        <input class="input" type="text" name="sku" value="{{ old('sku',$variant->sku) }}" required>
    </div>
    <div class="field">
        <label>Regular price<span class="req">*</span></label>
        <input class="input" type="number" step="0.01" min="0" name="regular_price" value="{{ old('regular_price',$variant->regular_price) }}" required>
    </div>
    <div class="field">
        <label>Sale price</label>
        <input class="input" type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price',$variant->sale_price) }}">
    </div>
    <div class="field">
        <label>Stock<span class="req">*</span></label>
        <input class="input" type="number" min="0" name="stock" value="{{ old('stock',$variant->stock) }}" required>
    </div>
    <div class="field">
        <label>Display order<span class="req">*</span></label>
        <input class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order',$variant->sort_order ?? 0) }}" required>
    </div>
    <div class="field">
        <label>Variant image</label>
        <input class="input" type="file" name="variant_image" accept="image/png,image/webp,image/jpeg">
        @if(!empty($variant->image))<small class="subtle">A variant image is currently set.</small>@endif
    </div>
    <div class="full variant-toggles">
        <label class="check-label"><input type="checkbox" name="is_default" value="1" @checked(old('is_default',$variant->is_default))> Default color</label>
        <label class="check-label"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$variant->exists?$variant->is_active:true))> Enabled</label>
    </div>
</div>
