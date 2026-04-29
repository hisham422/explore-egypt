@extends('admin.layouts.app', [
    'title' => 'Admin | Create Region',
    'heading' => 'Create Region',
    'subheading' => 'Add a new region to the catalog',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Region Details'])
        <form method="POST" action="{{ route('admin.regions.store') }}" enctype="multipart/form-data">
            @include('admin.regions._form')
        </form>
    @endcomponent
@endsection
