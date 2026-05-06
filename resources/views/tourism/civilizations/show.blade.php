<x-tourism-layout title="Explore Egypt | {{ $civilization->name }}">
    <section class="section-block page-top">
        <div class="container">
            <section
                class="civilization-hero detail-card civilization-detail-card"
                x-data="{ playing: true, toggle() { const video = this.$refs.heroVideo; if (!video) return; if (video.paused) { video.play(); this.playing = true; } else { video.pause(); this.playing = false; } } }"
            >
                <div class="civilization-hero-media">
                    <video
                        class="civilization-hero-video"
                        x-ref="heroVideo"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="metadata"
                        poster="{{ $civilization->image ? $civilization->imageUrl('1400x620') : '' }}"
                    >
                        <source src="{{ $heroVideoUrl }}">
                    </video>

                    {{-- Fallback image removed to prefer video display in hero --}}

                    <div class="civilization-hero-overlay"></div>
                </div>

                <div class="civilization-hero-content">
                    <span class="category-badge category-badge--civilization">Civilization</span>
                    <h1>{{ $civilization->name }}</h1>
                    <p>{{ $civilization->description ?: 'Discover the history, culture, and attractions connected to this civilization.' }}</p>
                </div>
            </section>

            <div id="civilization-timeline">
                <x-civilization-timeline :civilization="$civilization" />
            </div>

            <section class="civilization-attractions-section">
                <div class="section-head section-head-stack">
                    <h2>Attractions in This Civilization</h2>
                    <p>Browse landmarks and destinations connected to this era.</p>
                </div>

                <div class="grid cols-3">
                    @forelse($attractions as $attraction)
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
                                    <p class="meta">★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }} · {{ $attraction->reviews_count }}</p>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="empty">No attractions found for this civilization yet. Explore other eras or check back for new additions.</p>
                    @endforelse
                </div>
            </section>

            <div class="pagination-wrap">
                {{ $attractions->links() }}
            </div>
        </div>
    </section>
</x-tourism-layout>
