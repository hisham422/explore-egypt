@extends('admin.layouts.app', [
    'title' => 'Admin | Reviews',
    'heading' => 'Reviews',
    'subheading' => 'Manage user ratings and comments',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by user, attraction, or comment">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.reviews.create') }}" class="admin-btn admin-btn-primary">New Review</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $reviews->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'User', 'Attraction', 'Rating', 'Comment', 'Actions']])
        @forelse($reviews as $review)
            <tr>
                <td>{{ $review->id }}</td>
                <td>{{ $review->user?->name ?? '-' }}</td>
                <td>{{ $review->attraction?->name ?? '-' }}</td>
                <td>{{ $review->rating }}/5</td>
                <td>{{ \Illuminate\Support\Str::limit($review->comment ?: '-', 90) }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.reviews.edit', $review) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');">
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
                        <p style="margin:0 0 8px;">No reviews found.</p>
                        <p class="admin-help" style="margin:0;">Try another search or create a new review entry.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $reviews->links() }}</div>
@endsection
