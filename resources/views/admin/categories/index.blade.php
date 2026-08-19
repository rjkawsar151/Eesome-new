@extends('layouts.admin')
@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
<div class="page-head">
    <h1 class="title">Categories</h1>
    <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">Add category</a>
</div>

<div class="card table-wrap" style="margin-top:1rem">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Order</th>
                <th>Status</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><code>{{ $c->slug }}</code></td>
                    <td><span class="badge {{ $c->products_count > 0 ? 'badge-green' : '' }}">{{ $c->products_count }} products</span></td>
                    <td>{{ $c->sort_order }}</td>
                    <td>
                        <span class="badge {{ $c->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $c->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <div style="display:inline-flex;gap:.4rem;align-items:center">
                            <a class="btn btn-soft btn-sm" href="{{ route('admin.categories.edit', $c) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($c->name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top:1rem">
    {{ $categories->links() }}
</div>
@endsection