@extends('admin.layouts.app', [
    'title' => 'Admin | Create Review',
    'heading' => 'Create Review',
    'subheading' => 'Add a review on behalf of users',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Review Details'])
        <form method="POST" action="{{ route('admin.reviews.store') }}">
            @include('admin.reviews._form')
        </form>
    @endcomponent
@endsection
