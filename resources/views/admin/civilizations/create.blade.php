@extends('admin.layouts.app', [
    'title' => 'Admin | Create Civilization',
    'heading' => 'Create Civilization',
    'subheading' => 'Add a new civilization to the catalog',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Civilization Details'])
        <form method="POST" action="{{ route('admin.civilizations.store') }}" enctype="multipart/form-data">
            @include('admin.civilizations._form')
        </form>
    @endcomponent
@endsection
