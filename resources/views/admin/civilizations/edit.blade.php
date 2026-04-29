@extends('admin.layouts.app', [
    'title' => 'Admin | Edit Civilization',
    'heading' => 'Edit Civilization',
    'subheading' => 'Update civilization information',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Civilization Details'])
        <form method="POST" action="{{ route('admin.civilizations.update', $civilization) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.civilizations._form')
        </form>
    @endcomponent
@endsection
