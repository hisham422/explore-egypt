@extends('admin.layouts.app', [
    'title' => 'Admin | Edit Attraction',
    'heading' => 'Edit Attraction',
    'subheading' => 'Update attraction details',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Attraction Details'])
        <form method="POST" action="{{ route('admin.attractions.update', $attraction) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.attractions._form')
        </form>
    @endcomponent
@endsection
