@extends('admin.layouts.app', [
    'title' => 'Admin | Civilizations',
    'heading' => 'Civilizations',
    'subheading' => 'Manage civilizations shown on the platform',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.civilizations.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name or description">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.civilizations.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.civilizations.create') }}" class="admin-btn admin-btn-primary">New Civilization</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $civilizations->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'Name', 'Description', 'Actions']])
        @forelse($civilizations as $civilization)
            <tr>
                <td>{{ $civilization->id }}</td>
                <td>{{ $civilization->name }}</td>
                <td>{{ \Illuminate\Support\Str::limit($civilization->description, 120) }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.civilizations.edit', $civilization) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.civilizations.destroy', $civilization) }}" onsubmit="return confirm('Delete this civilization?');">
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
                        <p style="margin:0 0 8px;">No civilizations found.</p>
                        <p class="admin-help" style="margin:0;">Try a different search, or create the first civilization.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $civilizations->links() }}</div>
@endsection
