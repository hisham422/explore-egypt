<x-tourism-layout title="Explore Egypt | Explore">
    <section class="section-block page-top">
        <div class="container">
            <div class="section-head section-head-stack">
                <h1>Explore Attractions</h1>
                <p>Search, filter, and browse Egypt's destinations in one place.</p>
            </div>

            <form action="{{ route('explore') }}" method="GET" class="explore-filters">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search places, civilizations, or regions" aria-label="Search attractions">

                <select name="civilization_id" aria-label="Filter by civilization">
                    <option value="">All civilizations</option>
                    @foreach($civilizations as $civilization)
                        <option value="{{ $civilization->id }}" @selected((int) ($filters['civilization_id'] ?? 0) === (int) $civilization->id)>{{ $civilization->name }}</option>
                    @endforeach
                </select>

                <select name="region_id" aria-label="Filter by region">
                    <option value="">All regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected((int) ($filters['region_id'] ?? 0) === (int) $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>

                <select name="sort" aria-label="Sort attractions">
                    <option value="">Newest</option>
                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Top rated</option>
                </select>

                <button type="submit" class="btn btn-primary">Apply</button>
            </form>

            @if($search)
                <p class="search-info">Search results for "{{ $search }}"</p>
            @endif

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
                                <div class="row-between">
                                    <h3>{{ $attraction->name }}</h3>
                                </div>
                                <p>{{ $attraction->location }}</p>
                                <p class="meta">★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }} · {{ $attraction->reviews_count }}</p>
                            </div>
                        </a>
                    </article>
                @empty
                    <p class="empty">No attractions matched this search yet. Try another keyword or clear one of the filters to discover more places.</p>
                @endforelse
            </div>

            <div class="pagination-wrap">
                {{ $attractions->links() }}
            </div>
        </div>
    </section>
</x-tourism-layout>
