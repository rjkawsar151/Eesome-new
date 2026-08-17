@extends('layouts.admin')
@section('title',$product->exists?'Edit product':'Add product')
@section('heading','Products')

@push('styles')
<style>
.variants-builder {
    margin-top: 1rem;
    padding: 1.15rem;
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: 0.75rem;
}
.variants-builder-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.variants-container {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}
.variant-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 0.65rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    transition: border-color .15s ease;
}
.variant-card:hover {
    border-color: #cbd5e1;
}
.variant-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 0.65rem;
    margin-bottom: 0.85rem;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.variant-card-title {
    display: flex;
    align-items: center;
    gap: 0.55rem;
}
.var-swatch-preview {
    display: inline-block;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.15);
    flex-shrink: 0;
}
.var-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.85rem;
}
@media (max-width: 720px) {
    .variants-builder {
        padding: 0.85rem;
    }
    .variants-builder-head {
        flex-direction: column;
        align-items: flex-start;
    }
    .variants-builder-head .btn {
        width: 100%;
    }
    .variant-card {
        padding: 0.85rem;
    }
    .var-grid {
        grid-template-columns: 1fr;
    }
    .variant-card-head {
        flex-direction: row;
        justify-content: space-between;
    }
    .btn-remove-row {
        width: auto;
    }
}
</style>
@endpush

@section('content')
<h1 class="title">{{ $product->exists?'Edit product':'Add product' }}</h1>
<form class="card" style="margin-top:1rem" method="POST" enctype="multipart/form-data" action="{{ $product->exists?route('admin.products.update',$product):route('admin.products.store') }}">
    @csrf
    @if($product->exists)@method('PUT')@endif

    <div class="form-section">
        <h2 class="form-section-title">Basics</h2>
        <div class="form-grid">
            <div class="field">
                <label>Name<span class="req">*</span></label>
                <input class="input" type="text" name="name" id="product_name" value="{{ old('name',$product->name) }}" required>
            </div>
            <div class="field">
                <label>Slug<span class="req">*</span></label>
                <input class="input" type="text" name="slug" value="{{ old('slug',$product->slug) }}" required>
            </div>
            <div class="field">
                <label>SKU</label>
                <input class="input" type="text" name="sku" id="product_sku" value="{{ old('sku',$product->sku) }}">
            </div>
            <div class="field">
                <label>Badge</label>
                <input class="input" type="text" name="badge_text" value="{{ old('badge_text',$product->badge_text) }}" placeholder="e.g. New arrival">
            </div>
            <div class="field full">
                <label>Category<span class="req">*</span></label>
                <select class="select" name="category_id" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field full">
                <label>Brand</label>
                <select class="select" name="brand_id">
                    <option value="">No brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(old('brand_id',$product->brand_id)==$brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field full">
                <label>Tags</label>
                <select class="select" name="tag_ids[]" multiple size="4">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id,old('tag_ids',$product->tags?->pluck('id')->all()??[])))>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">Pricing &amp; stock</h2>
        <div class="form-grid">
            <div class="field">
                <label>Price<span class="req">*</span></label>
                <input class="input" type="number" step="0.01" min="0" name="price" id="product_price" value="{{ old('price',$product->price) }}" required>
            </div>
            <div class="field">
                <label>Sale price</label>
                <input class="input" type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price',$product->discount_price) }}">
            </div>
            <div class="field">
                <label>Stock<span class="req">*</span></label>
                <input class="input" type="number" min="0" name="stock" id="product_stock" value="{{ old('stock',$product->stock) }}" required>
            </div>
            <div class="field">
                <label>Display / hero order<span class="req">*</span></label>
                <input class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order',$product->sort_order) }}" required>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">Description &amp; SEO</h2>
        <div class="form-grid">
            <div class="field full">
                <label>Description</label>
                <div class="rich-toolbar">
                    <button type="button" data-command="bold"><b>B</b></button>
                    <button type="button" data-command="italic"><i>I</i></button>
                    <button type="button" data-command="insertUnorderedList">List</button>
                    <button type="button" data-command="formatBlock" data-value="h3">Heading</button>
                </div>
                <div class="textarea rich-editor" contenteditable="true">{!! old('description',$product->description) !!}</div>
                <textarea hidden name="description" class="rich-source">{{ old('description',$product->description) }}</textarea>
            </div>
            <div class="field full">
                <label>SEO title</label>
                <input class="input" type="text" name="meta_title" value="{{ old('meta_title',$product->meta_title) }}">
            </div>
            <div class="field full">
                <label>SEO description</label>
                <textarea class="textarea" name="meta_description">{{ old('meta_description',$product->meta_description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">Images</h2>
        <div class="form-grid">
            <div class="field full">
                <label>Images (PNG, WebP, JPG; max 5MB each)</label>
                <input class="input" type="file" name="images[]" multiple accept="image/png,image/webp,image/jpeg">
            </div>
            @if(count($product->images??[]))
                <div class="field full">
                    <label>Current images</label>
                    <div class="image-grid">
                        @foreach($product->images as $img)
                            <div class="img-item">
                                <img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img->image_path) }}" alt="">
                                <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('Are you sure you want to remove this image?')) document.getElementById('image-{{ $img->id }}').submit();">Remove</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">Variants &amp; publishing</h2>
        <div class="form-grid">
            <div class="field full">
                <label class="check-label">
                    <input type="hidden" name="has_variants" value="0">
                    <input type="checkbox" id="has_variants_toggle" name="has_variants" value="1" @checked(old('has_variants', $product->has_variants || $product->variants->count()))> This product has color variants
                </label>
                <small class="subtle">Enable to add color options directly while adding or editing this product on mobile and desktop.</small>
            </div>

            <!-- Inline Variants Builder for Mobile & Desktop -->
            <div class="field full" id="variants-builder-section" style="{{ old('has_variants', $product->has_variants || $product->variants->count()) ? '' : 'display:none;' }}">
                <div class="variants-builder">
                    <div class="variants-builder-head">
                        <div>
                            <h3 style="margin:0 0 .25rem;font-size:1rem;font-weight:700">Product Color Variants</h3>
                            <p class="subtle" style="margin:0;font-size:.85rem">Add and manage color variants. Each variant can carry its own SKU, price, stock, and image.</p>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-variant">+ Add Variant</button>
                    </div>

                    <div id="variants-container" class="variants-container">
                        @php
                            $oldVariants = old('variants');
                            $existingVariants = $product->exists ? $product->variants : collect();
                        @endphp

                        @if($oldVariants && is_array($oldVariants))
                            @foreach($oldVariants as $i => $v)
                                @include('admin.products._variant-row', ['index' => $i, 'variantData' => $v, 'isOld' => true])
                            @endforeach
                        @elseif($existingVariants->count() > 0)
                            @foreach($existingVariants as $i => $v)
                                @include('admin.products._variant-row', ['index' => $i, 'variantModel' => $v, 'isOld' => false])
                            @endforeach
                        @endif
                    </div>

                    <div id="variants-empty-notice" class="subtle" style="padding:1.25rem;text-align:center;border:1px dashed var(--line);border-radius:.5rem;margin-top:.5rem;{{ ($oldVariants || $existingVariants->count() > 0) ? 'display:none;' : '' }}">
                        No variants added yet. Click <strong>"+ Add Variant"</strong> to add your first color option.
                    </div>
                </div>
            </div>

            <div class="field full">
                <div class="check-row">
                    @foreach(['is_active'=>'Active','is_featured'=>'Homepage hero','is_new'=>'New','is_preorder'=>'Preorder'] as $key=>$label)
                        <label class="check-label"><input type="checkbox" name="{{ $key }}" value="1" @checked(old($key,$product->$key??($key==='is_active')))>{{ $label }}</label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions full" style="border-top:1px solid var(--line);padding-top:1rem;margin-top:1rem">
        <button id="save-product" class="btn btn-primary"><span>Save product</span></button>
        @if($product->exists)
            <a class="btn btn-soft" href="{{ route('admin.products.index') }}">Back to products</a>
        @endif
    </div>
</form>

{{-- Image Deletion Forms (Outside main product form) --}}
@if($product->exists)
    @foreach($product->images as $img)
        <form id="image-{{ $img->id }}" method="POST" action="{{ route('admin.products.images.destroy',[$product,$img]) }}" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif

{{-- Template for dynamic variant rows --}}
<template id="variant-row-template">
    <div class="variant-card" data-index="__INDEX__">
        <div class="variant-card-head">
            <div class="variant-card-title">
                <span class="var-swatch-preview" style="background-color: #be185d"></span>
                <strong>Variant #<span class="var-num">__NUM__</span></strong>
                <span class="badge var-color-badge">New variant</span>
            </div>
            <button type="button" class="btn btn-danger btn-sm btn-remove-row">Remove</button>
        </div>
        <div class="var-grid">
            <div class="field">
                <label>Color name<span class="req">*</span></label>
                <input class="input var-color-input" type="text" name="variants[__INDEX__][color_name]" required placeholder="e.g. Rose Pink">
            </div>
            <div class="field">
                <label>Color swatch<span class="req">*</span></label>
                <div class="swatch-wrap">
                    <input class="input swatch var-swatch-input" type="color" name="variants[__INDEX__][color_code]" value="#be185d" required>
                    <input class="input var-hex-input" type="text" value="#be185d" placeholder="#be185d" style="width:90px;font-family:monospace;font-size:.85rem;padding:.4rem .5rem">
                </div>
            </div>
            <div class="field">
                <label>Variant SKU<span class="req">*</span></label>
                <input class="input var-sku-input" type="text" name="variants[__INDEX__][sku]" required placeholder="e.g. BAG-ROSE">
            </div>
            <div class="field">
                <label>Regular price<span class="req">*</span></label>
                <input class="input var-price-input" type="number" step="0.01" min="0" name="variants[__INDEX__][regular_price]" required>
            </div>
            <div class="field">
                <label>Sale price</label>
                <input class="input" type="number" step="0.01" min="0" name="variants[__INDEX__][sale_price]" placeholder="Optional">
            </div>
            <div class="field">
                <label>Stock<span class="req">*</span></label>
                <input class="input var-stock-input" type="number" min="0" name="variants[__INDEX__][stock]" value="10" required>
            </div>
            <div class="field">
                <label>Display order</label>
                <input class="input var-order-input" type="number" min="0" name="variants[__INDEX__][sort_order]" value="__INDEX__">
            </div>
            <div class="field">
                <label>Variant image</label>
                <input class="input" type="file" name="variants[__INDEX__][image]" accept="image/png,image/webp,image/jpeg">
            </div>
            <div class="full variant-toggles">
                <label class="check-label"><input type="checkbox" name="variants[__INDEX__][is_default]" value="1"> Default color</label>
                <label class="check-label"><input type="checkbox" name="variants[__INDEX__][is_active]" value="1" checked> Enabled</label>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Rich text editor support
document.querySelectorAll('.rich-editor').forEach((editor)=>{
    const source=editor.parentElement.querySelector('.rich-source');
    editor.closest('form').addEventListener('submit',()=>source.value=editor.innerHTML);
    editor.parentElement.querySelectorAll('[data-command]').forEach((button)=>button.addEventListener('click',()=>{editor.focus();document.execCommand(button.dataset.command,false,button.dataset.value||null)}))
});

// Save button feedback
document.querySelector('#save-product')?.closest('form')?.addEventListener('submit',()=>{
    const button=document.querySelector('#save-product');
    button.disabled=true;
    button.querySelector('span').textContent='Saving…'
});

// Auto slug generation
(function(){
    const form=document.querySelector('#save-product')?.closest('form');
    if(!form)return;
    const name=form.querySelector('input[name="name"]');
    const slug=form.querySelector('input[name="slug"]');
    const productId={{ $product->id ?? 'null' }};
    const toSlug=(v)=>String(v||'').toLowerCase().replace(/[^a-z0-9\s-]+/g,'').trim().replace(/[\s-]+/g,'-').replace(/^-+|-+$/g,'');
    name.addEventListener('input',()=>{slug.value=toSlug(name.value)});
    let ensuring=false;
    form.addEventListener('submit',(e)=>{
        if(ensuring||!name.value.trim()||slug.value!==toSlug(name.value))return;
        e.preventDefault();
        ensuring=true;
        fetch('{{ route('admin.products.slug-check') }}?name='+encodeURIComponent(name.value)+'&ignore='+encodeURIComponent(productId),{headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then((r)=>r.json())
            .then((d)=>{slug.value=d.slug;form.submit()})
            .catch(()=>form.submit())
    });
})();

// Dynamic Variant Builder for Mobile & Desktop
(function(){
    const toggle = document.getElementById('has_variants_toggle');
    const builderSection = document.getElementById('variants-builder-section');
    const container = document.getElementById('variants-container');
    const emptyNotice = document.getElementById('variants-empty-notice');
    const btnAdd = document.getElementById('btn-add-variant');
    const template = document.getElementById('variant-row-template');

    if (!toggle || !builderSection || !container || !template) return;

    function updateEmptyNotice() {
        const count = container.querySelectorAll('.variant-card').length;
        if (emptyNotice) {
            emptyNotice.style.display = count === 0 ? 'block' : 'none';
        }
    }

    function reindexVariants() {
        const cards = container.querySelectorAll('.variant-card');
        cards.forEach((card, index) => {
            card.dataset.index = index;
            const numSpan = card.querySelector('.var-num');
            if (numSpan) numSpan.textContent = index + 1;

            const orderInput = card.querySelector('.var-order-input');
            if (orderInput && !orderInput.value) orderInput.value = index;

            // Update input names
            card.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.startsWith('variants[')) {
                    input.setAttribute('name', name.replace(/variants\[\d+\]/, `variants[${index}]`));
                }
            });
        });
        updateEmptyNotice();
    }

    function initVariantCardEvents(card) {
        const colorInput = card.querySelector('.var-color-input');
        const swatchInput = card.querySelector('.var-swatch-input');
        const hexInput = card.querySelector('.var-hex-input');
        const swatchPreview = card.querySelector('.var-swatch-preview');
        const colorBadge = card.querySelector('.var-color-badge');
        const skuInput = card.querySelector('.var-sku-input');
        const btnRemove = card.querySelector('.btn-remove-row');

        // Color name change -> update badge and SKU suggestion
        if (colorInput) {
            colorInput.addEventListener('input', () => {
                const val = colorInput.value.trim();
                if (colorBadge) colorBadge.textContent = val || 'Untitled';
                
                // Auto-suggest variant SKU if blank
                const baseSku = document.getElementById('product_sku')?.value.trim() || document.getElementById('product_name')?.value.trim() || '';
                if (val && baseSku && (!skuInput.value || skuInput.dataset.autoGenerated === 'true')) {
                    const cleanColor = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    const cleanBase = baseSku.toUpperCase().replace(/[^A-Z0-9-]/g, '');
                    skuInput.value = `${cleanBase}-${cleanColor}`;
                    skuInput.dataset.autoGenerated = 'true';
                }
            });
        }

        if (skuInput) {
            skuInput.addEventListener('input', () => {
                skuInput.dataset.autoGenerated = 'false';
            });
        }

        // Color picker <-> Hex input sync
        if (swatchInput && hexInput) {
            swatchInput.addEventListener('input', () => {
                hexInput.value = swatchInput.value;
                if (swatchPreview) swatchPreview.style.backgroundColor = swatchInput.value;
            });
            hexInput.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) {
                    swatchInput.value = hexInput.value;
                    if (swatchPreview) swatchPreview.style.backgroundColor = hexInput.value;
                }
            });
        }

        // Remove row
        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                card.remove();
                reindexVariants();
            });
        }
    }

    function addVariantRow(defaults = {}) {
        const index = container.querySelectorAll('.variant-card').length;
        let html = template.innerHTML
            .replace(/__INDEX__/g, index)
            .replace(/__NUM__/g, index + 1);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
        const card = tempDiv.firstChild;

        // Auto-fill price and stock defaults from product basics if available
        const prodPrice = document.getElementById('product_price')?.value;
        const prodStock = document.getElementById('product_stock')?.value;
        const prodSku = document.getElementById('product_sku')?.value;

        const priceInput = card.querySelector('.var-price-input');
        if (priceInput && prodPrice) priceInput.value = prodPrice;

        const stockInput = card.querySelector('.var-stock-input');
        if (stockInput && prodStock) stockInput.value = prodStock;

        // If it's the first variant, make it default by default
        if (index === 0) {
            const defaultCheck = card.querySelector('input[name*="[is_default]"]');
            if (defaultCheck) defaultCheck.checked = true;
        }

        container.appendChild(card);
        initVariantCardEvents(card);
        reindexVariants();

        // Focus the color input of the new card
        const newColorInput = card.querySelector('.var-color-input');
        if (newColorInput) newColorInput.focus();

        return card;
    }

    // Initialize existing variant cards
    container.querySelectorAll('.variant-card').forEach(initVariantCardEvents);
    updateEmptyNotice();

    // Add variant button click
    btnAdd.addEventListener('click', () => {
        addVariantRow();
    });

    // Toggle has_variants checkbox
    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            builderSection.style.display = 'block';
            if (container.querySelectorAll('.variant-card').length === 0) {
                addVariantRow();
            }
        } else {
            builderSection.style.display = 'none';
        }
    });

    // Ensure on submit, if has_variants is unchecked, variant inputs are disabled
    const form = toggle.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            if (!toggle.checked) {
                builderSection.querySelectorAll('input, select').forEach(input => {
                    input.disabled = true;
                });
            }
        });
    }
})();
</script>
@endpush
