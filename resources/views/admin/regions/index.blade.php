@extends('admin.layouts.app', [
    'title' => 'Admin | Regions',
    'heading' => 'Regions',
    'subheading' => 'Manage geographic regions displayed in the app',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.regions.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name or description">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.regions.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.regions.create') }}" class="admin-btn admin-btn-primary">New Region</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $regions->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'Name', 'Description', 'Actions']])
        @forelse($regions as $region)
            <tr>
                <td>{{ $region->id }}</td>
                <td>{{ $region->name }}</td>
                <td>{{ \Illuminate\Support\Str::limit($region->description ?: '-', 120) }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.regions.edit', $region) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" onsubmit="return confirm('Delete this region?');">
                            @csrf
                            @method('DELETE')
                            <button class="admin-btn admin-btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <div class="admin-card-empty">
                        <p style="margin:0 0 8px;">No regions found.</p>
                        <p class="admin-help" style="margin:0;">Try another search term, or create your first region.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $regions->links() }}</div>
@endsection
