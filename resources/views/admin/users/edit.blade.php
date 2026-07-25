@extends('layouts.admin')
@section('title','Edit User')
@section('heading','Users')
@section('content')
<h1 class="title">Edit {{ $user->name }}</h1><p class="subtle">Update profile details and access level.</p><form class="card" style="margin-top:1.5rem;max-width:850px" method="POST" action="{{ route('admin.users.update',$user) }}">@csrf @method('PUT') @include('admin.users._form')</form>
@endsection
