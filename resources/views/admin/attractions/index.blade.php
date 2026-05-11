@extends('admin.layouts.app', [
    'title' => 'Admin | Attractions',
    'heading' => 'Attractions',
    'subheading' => 'Manage attractions, activities, beaches, and coastal cities',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.attractions.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name, location, or description">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.attractions.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.attractions.create') }}" class="admin-btn admin-btn-primary">New Attraction</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $attractions->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'Name', 'Type', 'Civilization', 'Governorate', 'City', 'Location', 'Gallery', 'Actions']])
        @forelse($attractions as $attraction)
            <tr>
                <td>{{ $attraction->id }}</td>
                <td>{{ $attraction->name }}</td>
                <td>{{ ucfirst($attraction->type) }}</td>
                <td>{{ $attraction->civilization?->name ?? '-' }}</td>
                <td>{{ $attraction->region?->name ?? '-' }}</td>
                <td>{{ $attraction->city ?: '-' }}</td>
                <td>{{ $attraction->location ?: '-' }}</td>
                <td>{{ $attraction->images_count }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.attractions.edit', $attraction) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.attractions.destroy', $attraction) }}" onsubmit="return confirm('Delete this attraction?');">
                            @csrf
                            @method('DELETE')
                            <button class="admin-btn admin-btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">
                    <div class="admin-card-empty">
                        <p style="margin:0 0 8px;">No attractions found.</p>
                        <p class="admin-help" style="margin:0;">Adjust your search, or add a new attraction.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $attractions->links() }}</div>
@endsection
