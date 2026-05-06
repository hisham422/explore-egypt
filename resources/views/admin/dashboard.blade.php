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
            <h2 class="admin-section-title">Recent Reviews</h2>
            @if($recentReviews->isEmpty())
                <p class="admin-help admin-empty">No reviews yet.</p>
            @else
                <ul class="admin-list">
                    @foreach($recentReviews as $review)
                        <li>
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
            <h2 class="admin-section-title">Recent Users</h2>
            @if($recentUsers->isEmpty())
                <p class="admin-help admin-empty">No users yet.</p>
            @else
                <ul class="admin-list">
                    @foreach($recentUsers as $user)
                        <li>
                            <strong>{{ $user->name }}</strong>
                            <span class="admin-help">({{ $user->email }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
