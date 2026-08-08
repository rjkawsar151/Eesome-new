@extends('layouts.admin')
@section('title',$product->exists?'Edit product':'Add product')
@section('heading','Products')
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
                <input class="input" type="text" name="name" value="{{ old('name',$product->name) }}" required>
            </div>
            <div class="field">
                <label>Slug<span class="req">*</span></label>
                <input class="input" type="text" name="slug" value="{{ old('slug',$product->slug) }}" required>
            </div>
            <div class="field">
                <label>SKU</label>
                <input class="input" type="text" name="sku" value="{{ old('sku',$product->sku) }}">
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
                <input class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price',$product->price) }}" required>
            </div>
            <div class="field">
                <label>Sale price</label>
                <input class="input" type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price',$product->discount_price) }}">
            </div>
            <div class="field">
                <label>Stock<span class="req">*</span></label>
                <input class="input" type="number" min="0" name="stock" value="{{ old('stock',$product->stock) }}" required>
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
                                <button class="btn btn-danger btn-sm" form="image-{{ $img->id }}">Remove</button>
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
                    <input type="checkbox" name="has_variants" value="1" @checked(old('has_variants',$product->has_variants||$product->variants->count()))> This product has color variants
                </label>
                <small class="subtle">Save the product first, then add and manage its color rows in the <strong>Color variants</strong> section below.</small>
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
@if($product->exists)
    @foreach($product->images as $img)
        <form id="image-{{ $img->id }}" method="POST" action="{{ route('admin.products.images.destroy',[$product,$img]) }}">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
@if($product->exists)
    <section class="card" style="margin-top:1rem">
        <div class="section-head">
            <div>
                <h2 style="margin:0 0 .15rem">Color variants</h2>
                <p class="subtle" style="margin:0">Manage color rows for this product. Each variant can carry its own SKU, price, and stock.</p>
            </div>
        </div>
        <div class="form-section">
            <h3 class="form-section-title">Add a variant</h3>
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.products.variants.store',$product) }}">
                @csrf
                @include('admin.products._variant-fields',['variant'=>new \App\Models\ProductVariant])
                <div class="form-actions">
                    <button class="btn btn-primary">Add variant</button>
                </div>
            </form>
        </div>
        @forelse($product->variants as $variant)
            <div class="form-section">
                <h3 class="form-section-title">Variant {{ $loop->iteration }}<span class="badge" style="margin-left:.4rem">{{ $variant->color_name ?: 'Untitled' }}</span></h3>
                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.products.variants.update',[$product,$variant]) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.products._variant-fields',['variant'=>$variant])
                    <div class="form-actions">
                        <button class="btn btn-soft">Save variant</button>
                        <button class="btn btn-danger" form="delete-variant-{{ $variant->id }}">Delete</button>
                    </div>
                </form>
            </div>
            <form id="delete-variant-{{ $variant->id }}" method="POST" action="{{ route('admin.products.variants.destroy',[$product,$variant]) }}">
                @csrf
                @method('DELETE')
            </form>
        @empty
            <p class="subtle" style="margin-top:1rem">No variants yet. Add the first color above.</p>
        @endforelse
    </section>
@endif
@endsection
@push('scripts')
<script>
document.querySelectorAll('.rich-editor').forEach((editor)=>{
    const source=editor.parentElement.querySelector('.rich-source');
    editor.closest('form').addEventListener('submit',()=>source.value=editor.innerHTML);
    editor.parentElement.querySelectorAll('[data-command]').forEach((button)=>button.addEventListener('click',()=>{editor.focus();document.execCommand(button.dataset.command,false,button.dataset.value||null)}))
});
document.querySelector('#save-product')?.closest('form')?.addEventListener('submit',()=>{
    const button=document.querySelector('#save-product');
    button.disabled=true;
    button.querySelector('span').textContent='Saving…'
});
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
</script>
@endpush
