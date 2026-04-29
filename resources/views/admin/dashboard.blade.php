@extends('admin.layouts.app', [
    'title' => 'Admin Dashboard',
    'heading' => 'Dashboard',
    'subheading' => 'Quick overview of your tourism data',
])

@section('content')
    <div class="admin-grid">
        <article class="admin-card admin-stat">
            <h2>{{ number_format($stats['civilizations']) }}</h2>
            <p>Civilizations</p>
        </article>
        <article class="admin-card admin-stat">
            <h2>{{ number_format($stats['regions']) }}</h2>
            <p>Regions</p>
        </article>
        <article class="admin-card admin-stat">
            <h2>{{ number_format($stats['attractions']) }}</h2>
            <p>Attractions</p>
        </article>
        <article class="admin-card admin-stat">
            <h2>{{ number_format($stats['users']) }}</h2>
            <p>Users</p>
        </article>
        <article class="admin-card admin-stat">
            <h2>{{ number_format($stats['reviews']) }}</h2>
            <p>Reviews</p>
        </article>
    </div>

    <div class="admin-grid" style="grid-template-columns:1fr 1fr;">
        <section class="admin-card">
            <h2 style="margin:0 0 10px;color:#1f3d63;font-size:1.05rem;">Recent Reviews</h2>
            @if($recentReviews->isEmpty())
                <p class="admin-help" style="margin:0;">No reviews yet.</p>
            @else
                <ul style="margin:0;padding-left:18px;display:grid;gap:8px;">
                    @foreach($recentReviews as $review)
                        <li style="font-size:0.9rem;line-height:1.45;">
                            <strong>{{ $review->user?->name ?? 'User' }}</strong>
                            rated
                            <strong>{{ $review->attraction?->name ?? 'Attraction' }}</strong>
                            {{ $review->rating }}/5
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="admin-card">
            <h2 style="margin:0 0 10px;color:#1f3d63;font-size:1.05rem;">Recent Users</h2>
            @if($recentUsers->isEmpty())
                <p class="admin-help" style="margin:0;">No users yet.</p>
            @else
                <ul style="margin:0;padding-left:18px;display:grid;gap:8px;">
                    @foreach($recentUsers as $user)
                        <li style="font-size:0.9rem;line-height:1.45;">
                            <strong>{{ $user->name }}</strong>
                            <span class="admin-help">({{ $user->email }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
