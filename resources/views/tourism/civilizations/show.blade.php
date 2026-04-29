<x-tourism-layout title="Explore Egypt | {{ $civilization->name }}">
    <section class="section-block page-top">
        <div class="container">
            <article class="detail-card civilization-detail-card">
                <div class="detail-hero-wrap">
                    <x-image-frame class="detail-hero" :src="$civilization->image" :alt="$civilization->name" :label="$civilization->name" placeholder-size="1400x620" object-fit="cover" />
                </div>
                <div class="detail-body civilization-detail-body">
                    <div class="detail-header civilization-detail-header">
                        <span class="category-badge category-badge--civilization">Civilization</span>
                        <h1>{{ $civilization->name }}</h1>
                        <p class="detail-description">{{ $civilization->description ?: 'Discover the history, culture, and attractions connected to this civilization.' }}</p>
                    </div>
                </div>
            </article>

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
