@extends('layouts.admin')
@section('title','Add User')
@section('heading','Users')
@section('content')
<h1 class="title">Add user</h1><p class="subtle">Create a customer or team account.</p><form class="card" style="margin-top:1.5rem;max-width:850px" method="POST" action="{{ route('admin.users.store') }}">@csrf @include('admin.users._form')</form>
@endsection
