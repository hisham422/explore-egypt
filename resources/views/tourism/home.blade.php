<x-tourism-layout title="Explore Egypt | Home">
    <div class="home-page">
    <section class="hero-section hero-banner" style="background-image: linear-gradient(135deg, rgba(9, 20, 35, 0.88), rgba(30, 58, 95, 0.72)), url('{{ $heroImageUrl }}');">
        <div class="hero-overlay"></div>
        <div class="container hero-grid hero-grid--simple">
            <div class="hero-content hero-content--primary">
                <span class="hero-pill">Premium travel guide</span>
                <p class="kicker">Explore Egypt</p>
                <h1>Discover timeless landmarks through a polished travel experience</h1>
                <p class="hero-text">Browse curated civilizations, iconic regions, and must-see attractions with modern planning tools, smooth navigation, and community-driven ratings.</p>
                <div class="hero-actions">
                    <a href="{{ route('explore') }}" class="btn btn-primary">Start Exploring</a>
                    <a href="{{ route('civilizations.index') }}" class="btn btn-outline btn-light">Civilizations</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="section-head">
                <h2>Civilizations</h2>
                <a href="{{ route('civilizations.index') }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($civilizations as $civilization)
                    <a href="{{ route('civilizations.show', $civilization) }}" class="card card-link">
                        <x-image-frame :src="$civilization->image" :alt="$civilization->name" :label="$civilization->name" placeholder-size="800x500" />
                        <span class="category-badge">Civilization</span>
                        <div class="card-content">
                            <h3>{{ $civilization->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($civilization->description, 100) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Civilizations are on the way. Check back soon to begin your journey through Egypt's history.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="section-head">
                <h2>Regions</h2>
                <a href="{{ route('regions.index') }}">View all</a>
            </div>
            <div class="grid cols-4">
                @forelse($regions as $region)
                    <a href="{{ route('regions.show', $region) }}" class="card card-link">
                        <x-image-frame :src="$region->image" :alt="$region->name" :label="$region->name" placeholder-size="800x500" />
                        <span class="category-badge">Region</span>
                        <div class="card-content">
                            <h3>{{ $region->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($region->description ?: 'Egyptian governorate with diverse attractions.', 90) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">Regions will appear here soon. Explore again shortly to discover more destinations.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-block section-last home-featured-section">
        <div class="container">
            <div class="section-head">
                <h2>Featured Attractions</h2>
                <a href="{{ route('explore') }}">View all</a>
            </div>
            <div class="grid cols-3">
                @forelse($featuredAttractions as $attraction)
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
                        @else
                            <a href="{{ route('login') }}" class="favorite-toggle favorite-toggle--icon favorite-toggle--floating favorite-toggle--login" aria-label="Login to favorite">♡</a>
                        @endauth

                        <span
                            class="favorite-count-badge"
                            data-attraction-id="{{ $attraction->id }}"
                            data-favorites-count="{{ (int) ($attraction->favorites_count ?? 0) }}"
                            data-favorites-format="badge"
                        >{{ (int) ($attraction->favorites_count ?? 0) }}</span>

                        <a href="{{ route('attractions.show', $attraction) }}" class="attraction-card-link">
                            <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                            <span class="category-badge">Attraction</span>
                            <div class="card-content">
                                <h3>{{ $attraction->name }}</h3>
                                <p>{{ $attraction->location }}</p>
                                <p class="meta">★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }} · {{ $attraction->reviews_count }} ratings</p>
                            </div>
                        </a>
                    </article>
                @empty
                    <p class="empty">Featured attractions are being curated. Check back soon for handpicked places to visit.</p>
                @endforelse
            </div>
        </div>
    </section>
    </div>
</x-tourism-layout>
