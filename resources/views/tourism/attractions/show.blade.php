<x-tourism-layout title="Explore Egypt | {{ $attraction->name }}">
    <section class="section-block page-top">
        <div class="container">
            @php
                $currentFavorite = auth()->check() ? $attraction->favorites->first() : null;
                $galleryItems = collect();

                // Add main attraction image as first gallery item
                if ($attraction->image) {
                    $galleryItems->push([
                        'src' => $attraction->imageUrl('1200x620'),
                        'thumb' => $attraction->imageUrl('360x240'),
                        'alt' => $attraction->name,
                        'type' => 'image',
                    ]);
                }

                // Add all media (images and videos)
                foreach ($attraction->images as $galleryMedia) {
                    if ($galleryMedia->isVideo()) {
                        $galleryItems->push([
                            'src' => $galleryMedia->videoUrl(),
                            'thumb' => null,
                            'alt' => $attraction->name.' video',
                            'type' => 'video',
                        ]);
                    } else {
                        $galleryItems->push([
                            'src' => $galleryMedia->imageUrl('1200x620'),
                            'thumb' => $galleryMedia->imageUrl('360x240'),
                            'alt' => $attraction->name.' gallery image',
                            'type' => 'image',
                        ]);
                    }
                }

                // Ensure at least one item with fallback
                if ($galleryItems->isEmpty()) {
                    $galleryItems->push([
                        'src' => $attraction->imageUrl('1200x620'),
                        'thumb' => $attraction->imageUrl('360x240'),
                        'alt' => $attraction->name,
                        'type' => 'image',
                    ]);
                }

                $galleryItems = $galleryItems->unique('src')->values();
            @endphp
            @php
                $heroGalleryItem = $galleryItems->first();
            @endphp
            <article class="detail-card">
                <div class="detail-hero-wrap">
                    <x-image-frame class="detail-hero" :src="data_get($heroGalleryItem, 'src')" :alt="data_get($heroGalleryItem, 'alt', $attraction->name)" :label="$attraction->name" placeholder-size="1200x620" object-fit="cover" />
                </div>

                <div class="detail-body">
                    <div class="row-between">
                        <div>
                            <h1>{{ $attraction->name }}</h1>
                            <p class="muted">{{ $attraction->location }}</p>
                        </div>
                        @php
                            $favoritesCount = (int) ($attraction->favorites_count ?? 0);
                        @endphp
                        <div class="favorite-action-group">
                        @auth
                            <button
                                type="button"
                                class="btn favorite-toggle {{ $isFavorited ? 'btn-primary is-active' : 'btn-outline' }}"
                                data-attraction-id="{{ $attraction->id }}"
                                data-favorite-id="{{ $currentFavorite?->id }}"
                                data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
                                data-favorite-style="button"
                                data-favorite-endpoint="{{ url('/favorites') }}"
                                aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                                aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}"
                            >{{ $isFavorited ? 'Saved to Favorites' : 'Add to Favorites' }}</button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline">Login to add favorite</a>
                        @endauth
                            <span
                                class="favorite-count-inline"
                                data-attraction-id="{{ $attraction->id }}"
                                data-favorites-count="{{ $favoritesCount }}"
                                data-favorites-format="inline"
                            >{{ $favoritesCount }} saved</span>
                        </div>
                    </div>

                    <div class="tag-row">
                        <span class="tag">{{ $attraction->civilization?->name ?? 'Civilization' }}</span>
                        <span class="tag">{{ $attraction->region?->name ?? 'Region' }}</span>
                    </div>

                    <p class="detail-description">{{ $attraction->description }}</p>

                    <section class="attraction-gallery-section" aria-labelledby="attraction-gallery-heading">
                        <div class="row-between section-head" style="margin-bottom: 12px;">
                            <div>
                                <h2 id="attraction-gallery-heading" style="margin:0;">Media Gallery</h2>
                                <p class="muted" style="margin:4px 0 0;">Browse images and videos. Click any thumbnail to view in full.</p>
                            </div>
                        </div>

                        <div class="attraction-gallery" data-attraction-gallery>
                            <div class="detail-hero-main attraction-gallery__main" data-gallery-main-stage>
                                @if(data_get($heroGalleryItem, 'type') === 'video')
                                    <div class="gallery-video-player" style="position:relative;width:100%;background:#000;border-radius:12px;overflow:hidden;">
                                        <video
                                            class="detail-hero gallery-main-image"
                                            data-gallery-main-image
                                            data-media-type="video"
                                            controls
                                            controlsList="nodownload"
                                            playsinline
                                            style="width:100%;height:auto;display:block;"
                                        >
                                            <source src="{{ data_get($heroGalleryItem, 'src') }}" type="video/mp4" crossorigin="anonymous">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @else
                                    <button type="button" class="gallery-main-open" data-gallery-open aria-label="Open media gallery">
                                        <img
                                            class="detail-hero gallery-main-image"
                                            src="{{ data_get($heroGalleryItem, 'src') }}"
                                            alt="{{ data_get($heroGalleryItem, 'alt', $attraction->name) }}"
                                            data-gallery-main-image
                                            data-media-type="image"
                                        >
                                    </button>
                                @endif
                            </div>

                            <div class="attraction-gallery-thumbs" role="list" aria-label="Attraction gallery thumbnails">
                                @foreach($galleryItems as $index => $item)
                                    <button
                                        type="button"
                                        class="attraction-gallery-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                        data-gallery-thumb
                                        data-gallery-index="{{ $index }}"
                                        data-gallery-src="{{ $item['src'] }}"
                                        data-gallery-type="{{ $item['type'] }}"
                                        data-gallery-alt="{{ $item['alt'] }}"
                                        aria-label="View {{ $item['type'] === 'video' ? 'video' : 'image' }} {{ $index + 1 }}"
                                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                                    >
                                        @if($item['type'] === 'video')
                                            <div class="gallery-thumb-video" style="position:relative;width:100%;height:100%;background:#000;display:flex;align-items:center;justify-content:center;border-radius:8px;overflow:hidden;">
                                                <video class="gallery-thumb-video__preview" muted playsinline>
                                                    <source src="{{ $item['src'] }}" type="video/mp4" crossorigin="anonymous">
                                                </video>
                                                <span class="gallery-thumb-video__icon" aria-hidden="true">
                                                    <svg style="width:24px;height:24px;color:#fff;" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </span>
                                            </div>
                                        @else
                                            <x-image-frame :src="$item['thumb']" :alt="$item['alt']" :label="$item['alt']" placeholder-size="360x240" />
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                                
                        </div>
                    </section>

                    <div class="rating-box">
                        <p>Rating</p>
                        <h3 data-rating-average>★ {{ number_format((float) ($attraction->average_rating ?? 0), 1) }}</h3>
                        <span data-rating-count>{{ $attraction->reviews_count }} ratings</span>
                    </div>

                    <section class="review-section">
                        <div class="row-between review-header">
                            <h3>User Reviews</h3>
                            <span class="muted" data-total-reviews>{{ $attraction->reviews_count }} total reviews</span>
                        </div>

                        @auth
                            @php
                                $myReview = $attraction->reviews->firstWhere('user_id', auth()->id());
                            @endphp

                            <form
                                class="review-form"
                                data-review-form
                                data-review-url="{{ route('reviews.store', absolute: false) }}"
                                data-attraction-id="{{ $attraction->id }}"
                                data-current-user-id="{{ auth()->id() }}"
                                data-had-review="{{ $myReview ? 'true' : 'false' }}"
                                data-previous-rating="{{ (int) ($myReview?->rating ?? 0) }}"
                            >
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="attraction_id" value="{{ $attraction->id }}">
                                <input type="hidden" name="rating" value="{{ $myReview?->rating ?? 0 }}" data-review-rating>

                                <div class="star-group" data-star-group role="radiogroup" aria-label="Rate this attraction from 1 to 5 stars">
                                    @for($star = 1; $star <= 5; $star++)
                                        <button
                                            type="button"
                                            class="star-btn {{ ($myReview?->rating ?? 0) >= $star ? 'is-active' : '' }}"
                                            data-star-value="{{ $star }}"
                                            role="radio"
                                            aria-checked="{{ ($myReview?->rating ?? 0) === $star ? 'true' : 'false' }}"
                                            aria-label="Rate {{ $star }}"
                                        >★</button>
                                    @endfor
                                </div>

                                <textarea
                                    name="comment"
                                    rows="4"
                                    class="review-comment"
                                    placeholder="Write an optional comment about this attraction"
                                >{{ $myReview?->comment }}</textarea>

                                <div class="review-actions">
                                    <button type="submit" class="btn btn-primary">
                                        {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <p class="muted">Please <a href="{{ route('login') }}" class="review-login-link">log in</a> to submit your review.</p>
                        @endauth

                        <div class="review-list" data-review-list>
                            @forelse($attraction->reviews->sortByDesc('created_at') as $review)
                                <article class="review-item" data-review-item data-review-user-id="{{ $review->user_id }}">
                                    <div class="row-between">
                                        <p class="review-user">{{ $review->user?->name ?? 'User' }}</p>
                                        <p class="review-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                                    </div>
                                    @if($review->comment)
                                        <p class="review-comment-text">{{ $review->comment }}</p>
                                    @endif
                                </article>
                            @empty
                                <p class="empty">No reviews yet. Share your experience to help other travelers plan their visit.</p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </article>
        </div>
    </section>
</x-tourism-layout>
