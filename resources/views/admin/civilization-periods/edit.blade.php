@extends('admin.layouts.app', [
    'title' => 'Admin | Edit Civilization Period',
    'heading' => 'Edit Civilization Period',
    'subheading' => 'Update historical period information',
])

@section('content')
    @component('admin.components.form-card', ['title' => 'Period Details'])
        <form method="POST" action="{{ route('admin.civilization-periods.update', $period) }}">
            @method('PUT')
            @include('admin.civilization-periods._form')
        </form>
    @endcomponent
@endsection