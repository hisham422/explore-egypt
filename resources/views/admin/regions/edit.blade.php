@extends('admin.layouts.app', [
    'title' => 'Admin | Edit Region',
    'heading' => 'Edit Region',
    'subheading' => 'Update region information',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Region Details'])
        <form method="POST" action="{{ route('admin.regions.update', $region) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.regions._form')
        </form>
    @endcomponent
@endsection
