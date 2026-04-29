@extends('admin.layouts.app', [
    'title' => 'Admin | Create Attraction',
    'heading' => 'Create Attraction',
    'subheading' => 'Add a new attraction to your content catalog',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Attraction Details'])
        <form method="POST" action="{{ route('admin.attractions.store') }}" enctype="multipart/form-data">
            @include('admin.attractions._form')
        </form>
    @endcomponent
@endsection
