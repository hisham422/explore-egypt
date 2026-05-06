@extends('admin.layouts.app', [
    'title' => 'Admin | Civilization Periods',
    'heading' => 'Civilization Periods',
    'subheading' => 'Manage historical periods shown in the civilization timelines',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.civilization-periods.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by period, civilization, or description">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.civilization-periods.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.civilization-periods.create') }}" class="admin-btn admin-btn-primary">New Period</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $periods->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'Civilization', 'Period', 'Years', 'Description', 'Actions']])
        @forelse($periods as $period)
            <tr>
                <td>{{ $period->id }}</td>
                <td>{{ $period->civilization?->name ?? '-' }}</td>
                <td>{{ $period->title }}</td>
                <td>{{ $period->formatted_year_range }}</td>
                <td>{{ \Illuminate\Support\Str::limit($period->description, 90) }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.period-attractions.index', $period) }}">Attractions</a>
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.civilization-periods.edit', $period) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.civilization-periods.destroy', $period) }}" onsubmit="return confirm('Delete this civilization period?');">
                            @csrf
                            @method('DELETE')
                            <button class="admin-btn admin-btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <div class="admin-card-empty">
                        <p style="margin:0 0 8px;">No civilization periods found.</p>
                        <p class="admin-help" style="margin:0;">Try a different search, or create the first period.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $periods->links() }}</div>
@endsection