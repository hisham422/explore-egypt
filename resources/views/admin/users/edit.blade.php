@extends('admin.layouts.app', [
    'title' => 'Admin | Edit User',
    'heading' => 'Edit User',
    'subheading' => 'Update account information',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'User Details'])
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @method('PUT')
            @include('admin.users._form')
        </form>
    @endcomponent
@endsection
