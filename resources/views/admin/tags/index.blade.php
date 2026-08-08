@extends('layouts.admin') @section('title','Tags') @section('heading','Tags') @section('content')
<h1 class="title">Product tags</h1>
<form class="card form-grid" style="margin:1rem 0" method="POST" action="{{ route('admin.tags.store') }}">@csrf
    <div class="field"><label>Name</label><input class="input" name="name" required></div>
    <div class="field"><label>Slug</label><input class="input" name="slug" required></div>
    <div class="full"><button class="btn btn-primary">Add tag</button></div>
</form>
@foreach($tags as $tag)
    <form id="tag-update-{{ $tag->id }}" method="POST" action="{{ route('admin.tags.update',$tag) }}">@csrf @method('PUT')</form>
@endforeach
<div class="card table-wrap">
<table class="table"><thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Actions</th></tr></thead><tbody>
@foreach($tags as $tag)
<tr>
    <td><input class="input" name="name" value="{{ $tag->name }}" form="tag-update-{{ $tag->id }}" aria-label="Tag name"></td>
    <td><input class="input" name="slug" value="{{ $tag->slug }}" form="tag-update-{{ $tag->id }}" aria-label="Slug"></td>
    <td>{{ $tag->products_count }}</td>
    <td>
        <button class="btn btn-soft" form="tag-update-{{ $tag->id }}">Save</button>
        <form method="POST" action="{{ route('admin.tags.destroy',$tag) }}" style="display:inline" onsubmit="return confirm('Delete this tag?')">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form>
    </td>
</tr>
@endforeach
</tbody></table>
</div>
{{ $tags->links() }}
@endsection
