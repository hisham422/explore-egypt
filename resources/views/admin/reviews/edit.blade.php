@extends('admin.layouts.app', [
    'title' => 'Admin | Edit Review',
    'heading' => 'Edit Review',
    'subheading' => 'Update review details',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Review Details'])
        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
            @method('PUT')
            @include('admin.reviews._form')
        </form>
    @endcomponent
@endsection
