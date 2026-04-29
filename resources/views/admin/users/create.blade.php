@extends('admin.layouts.app', [
    'title' => 'Admin | Create User',
    'heading' => 'Create User',
    'subheading' => 'Add a new account manually',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'User Details'])
        <form method="POST" action="{{ route('admin.users.store') }}">
            @include('admin.users._form')
        </form>
    @endcomponent
@endsection
