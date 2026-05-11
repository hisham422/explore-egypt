<x-tourism-layout title="Explore Egypt | Home">
    <div class="home-page">
    <section class="hero-section hero-banner">
        <div class="hero-banner-media" aria-hidden="true">
            <video
                class="hero-banner-video"
                autoplay
                muted
                loop
                playsinline
                preload="metadata"
                poster="{{ asset('media/hero/home-hero.png') }}"
            >
                <source src="{{ asset('media/hero/home-hero-video.mp4') }}" type="video/mp4">
            </video>
            {{-- Fallback removed: prefer video as primary hero media --}}
        </div>
        <div class="hero-ambient-shape hero-ambient-shape--left" data-parallax="0.06" aria-hidden="true"></div>
        <div class="hero-ambient-shape hero-ambient-shape--right" data-parallax="0.09" aria-hidden="true"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-grid hero-grid--simple">
            <div class="hero-content hero-content--primary">
                <span class="hero-pill" data-auto-animate="fade-up" data-scroll-animate="fade-up" data-scroll-duration="0.85s" data-scroll-delay="0.06s">Trusted Egypt Travel Guide</span>
                <p class="kicker" data-auto-animate="typewriter" data-scroll-animate="fade-up" data-scroll-duration="1.2s" data-scroll-delay="0.12s">Explore Egypt</p>
                <h1 data-auto-animate="typewriter" data-scroll-animate="fade-up" data-scroll-duration="2.4s" data-scroll-delay="0.3s">Journey Through Millennia of Wonder</h1>
                <p class="hero-text" data-auto-animate="typewriter" data-scroll-animate="fade-up" data-scroll-duration="3.2s" data-scroll-delay="0.5s">Uncover ancient civilizations, iconic landmarks, and unforgettable stories across Egypt with curated routes built for modern travelers.</p>

                <div class="hero-actions">
                    <a href="{{ route('explore') }}" class="btn btn-primary" data-auto-animate="scale-up" data-scroll-animate="fade-up" data-scroll-delay="0.3s">Start Exploring</a>
                    <a href="{{ route('civilizations.index') }}" class="btn btn-outline btn-light" data-auto-animate="scale-up" data-scroll-animate="fade-up" data-scroll-delay="0.36s">Browse Civilizations</a>
                </div>

                <div class="hero-stats" data-auto-animate="fade-up" data-scroll-animate="fade-up" data-scroll-delay="0.42s" aria-label="Explore Egypt highlights">
                    <article data-auto-animate="zoom-in" data-scroll-animate="zoom-in" data-scroll-delay="0.44s">
                        <strong>5000+</strong>
                        <span>Years of History</span>
                    </article>
                    <article data-auto-animate="zoom-in" data-scroll-animate="zoom-in" data-scroll-delay="0.5s">
                        <strong>100+</strong>
                        <span>Curated Attractions</span>
                    </article>
                    <article data-auto-animate="zoom-in" data-scroll-animate="zoom-in" data-scroll-delay="0.56s">
                        <strong>5</strong>
                        <span>Major Civilizations</span>
                    </article>
                </div>
            </div>
        </div>
        <div class="hero-scroll-progress" aria-hidden="true">
            <span data-hero-progress-bar></span>
        </div>
    </section>

    <div class="hero-handoff" aria-hidden="true"></div>

    <!-- Civilizations section removed -->

    <section class="section-block" data-scroll-animate="fade-up">
        <div class="container">
            <div class="section-head">
                <h2>Explore Beaches 🌊</h2>
                <a href="{{ route('explore', ['type' => 'beach']) }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($beachAttractions as $attraction)
                    <a href="{{ route('attractions.show', $attraction) }}" class="card card-link" data-scroll-animate="zoom-in">
                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                        <span class="category-badge category-badge--beach">🌊 Beach</span>
                        <div class="card-content">
                            <h3>{{ $attraction->name }}</h3>
                            <p>{{ $attraction->location }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Beach attractions will appear here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block" data-scroll-animate="fade-up">
        <div class="container">
            <div class="section-head">
                <h2>Explore Activities 🎯</h2>
                <a href="{{ route('explore', ['type' => 'activity']) }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($activityAttractions as $attraction)
                    <a href="{{ route('attractions.show', $attraction) }}" class="card card-link" data-scroll-animate="zoom-in">
                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                        <span class="category-badge category-badge--activity">🎯 Activity</span>
                        <div class="card-content">
                            <h3>{{ $attraction->name }}</h3>
                            <p>{{ $attraction->location }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Activities like Nile tours and city experiences will appear here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block" data-scroll-animate="fade-up">
        <div class="container">
            <div class="section-head">
                <h2>Explore History 🏛️</h2>
                <a href="{{ route('explore', ['type' => 'historical']) }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($historicalAttractions as $attraction)
                    <a href="{{ route('attractions.show', $attraction) }}" class="card card-link" data-scroll-animate="zoom-in">
                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                        <span class="category-badge category-badge--historical">🏛️ Historical</span>
                        <div class="card-content">
                            <h3>{{ $attraction->name }}</h3>
                            <p>{{ $attraction->location }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Historical attractions will appear here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block" data-scroll-animate="fade-up">
        <div class="container">
            <div class="section-head">
                <h2>Explore Coastal Cities 🏝️</h2>
                <a href="{{ route('explore', ['type' => 'coastal']) }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($coastalAttractions as $attraction)
                    <a href="{{ route('attractions.show', $attraction) }}" class="card card-link" data-scroll-animate="zoom-in">
                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                        <span class="category-badge category-badge--coastal">🏝️ Coastal City</span>
                        <div class="card-content">
                            <h3>{{ $attraction->name }}</h3>
                            <p>{{ $attraction->location }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Coastal city attractions will appear here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block" data-scroll-animate="fade-up">
        <div class="container">
            <div class="section-head">
                <h2>Recommended for Summer ☀️</h2>
                <a href="{{ route('explore', ['type' => 'beach']) }}">Plan your trip</a>
            </div>
            <div class="grid cols-3">
                @forelse($summerRecommendations as $attraction)
                    <a href="{{ route('attractions.show', $attraction) }}" class="card card-link" data-scroll-animate="zoom-in">
                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                        <span class="category-badge {{ $attraction->type === 'coastal' ? 'category-badge--coastal' : 'category-badge--beach' }}">
                            {{ $attraction->type === 'coastal' ? '🏝️ Coastal City' : '🌊 Beach' }}
                        </span>
                        <div class="card-content">
                            <h3>{{ $attraction->name }}</h3>
                            <p>{{ $attraction->location }}</p>
                            <p class="meta">★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }} · {{ $attraction->reviews_count }} reviews</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Summer recommendations will appear once beach and coastal places are available.</p>
                @endforelse
            </div>
        </div>
    </section>

    @auth
        @if($recommendedAttractions->isNotEmpty())
            <section class="section-block home-recommended-section">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <h2>Recommended for You</h2>
                            <p>Based on the civilizations and regions you already like.</p>
                        </div>
                        <a href="{{ route('explore') }}">Explore more</a>
                    </div>
                    <div class="grid cols-3">
                        @foreach($recommendedAttractions as $attraction)
                            <article class="card attraction-card">
                                @auth
                                    <button
                                        type="button"
                                        class="favorite-toggle favorite-toggle--icon favorite-toggle--floating {{ $attraction->is_favorited ? 'is-active' : '' }}"
                                        data-attraction-id="{{ $attraction->id }}"
                                        data-favorite-id="{{ $attraction->current_favorite_id ?? '' }}"
                                        data-favorited="{{ $attraction->is_favorited ? 'true' : 'false' }}"
                                        data-favorite-style="icon"
                                        data-favorite-endpoint="{{ url('/favorites') }}"
                                        aria-pressed="{{ $attraction->is_favorited ? 'true' : 'false' }}"
                                        aria-label="{{ $attraction->is_favorited ? 'Remove from favorites' : 'Add to favorites' }}"
                                    >{{ $attraction->is_favorited ? '♥' : '♡' }}</button>
                                @endauth

                                <span
                                    class="favorite-count-badge"
                                    data-attraction-id="{{ $attraction->id }}"
                                    data-favorites-count="{{ (int) ($attraction->favorites_count ?? 0) }}"
                                    data-favorites-format="badge"
                                >{{ (int) ($attraction->favorites_count ?? 0) }}</span>

                                <a href="{{ route('attractions.show', $attraction) }}" class="attraction-card-link">
                                    <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                                    <span class="category-badge">Recommended</span>
                                    <div class="card-content">
                                        <h3>{{ $attraction->name }}</h3>
                                        <p>{{ $attraction->location }}</p>
                                        <p class="meta">★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }} · {{ $attraction->reviews_count }} reviews</p>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endauth
    </div>
</x-tourism-layout>
