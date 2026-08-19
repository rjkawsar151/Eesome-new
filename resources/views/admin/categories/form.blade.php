@extends('layouts.admin')
@section('title', 'Category')
@section('heading', 'Categories')

@section('content')
<div class="page-head">
    <h1 class="title">{{ $category->exists ? 'Edit' : 'Add' }} category</h1>
    <a class="btn btn-soft" href="{{ route('admin.categories.index') }}">← Back to categories</a>
</div>

<form class="card form-grid" style="margin-top:1rem" method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if($category->exists)
        @method('PUT')
    @endif

    <div class="field">
        <label for="name">Name</label>
        <input id="name" class="input" name="name" value="{{ old('name', $category->name) }}" required>
    </div>

    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" class="input" name="slug" value="{{ old('slug', $category->slug) }}" required>
        <small class="subtle">Use only the identifier, for example: purse</small>
    </div>

    <div class="field">
        <label for="sort_order">Order</label>
        <input id="sort_order" type="number" min="0" class="input" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" required>
    </div>

    <div class="field">
        <label for="meta_title">SEO Title</label>
        <input id="meta_title" class="input" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}">
    </div>

    <div class="field full">
        <label for="meta_description">SEO Description</label>
        <textarea id="meta_description" class="textarea" name="meta_description">{{ old('meta_description', $category->meta_description) }}</textarea>
    </div>

    <div class="field">
        <label for="image_upload">Image</label>
        <input id="image_upload" class="input" type="file" name="image_upload" accept="image/*">
        @if($category->image)
            <div style="margin-top:.4rem">
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="max-height:60px;border-radius:8px">
            </div>
        @endif
    </div>

    <div class="field full">
        <label class="check-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))> Active
        </label>
    </div>

    <div class="full" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-top:1rem">
        <button type="submit" class="btn btn-primary">Save Category</button>

        @if($category->exists)
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Are you sure you want to delete this category (\'{{ addslashes($category->name) }}\')?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Category</button>
            </form>
        @endif
    </div>
</form>
@endsection