@extends('admin.layouts.app', [
    'title' => 'Admin | Create Civilization Period',
    'heading' => 'Create Civilization Period',
    'subheading' => 'Add a new historical period to a civilization timeline',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Period Details'])
        <form method="POST" action="{{ route('admin.civilization-periods.store') }}">
            @include('admin.civilization-periods._form')
        </form>
    @endcomponent
@endsection