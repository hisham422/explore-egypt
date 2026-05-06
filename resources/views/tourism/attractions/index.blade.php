@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.0/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.0/dist/MarkerCluster.Default.css">
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.0/dist/leaflet.markercluster.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('attractions-map');
            const dataElement = document.getElementById('attractions-map-data');
            const searchInput = document.getElementById('attractions-map-search');
            const searchButton = document.getElementById('attractions-map-search-button');
            const searchStatus = document.getElementById('attractions-map-search-status');
            const civilizationFilter = document.getElementById('attractions-map-civilization-filter');
            const regionFilter = document.getElementById('attractions-map-region-filter');
            const ratingFilter = document.getElementById('attractions-map-rating-filter');
            const clearFiltersButton = document.getElementById('attractions-map-clear-filters');
            const mapStyleButtons = Array.from(document.querySelectorAll('[data-map-style]'));

            if (!mapElement || !dataElement || typeof window.L === 'undefined') {
                return;
            }

            const attractions = JSON.parse(dataElement.textContent || '[]');
            const styleStorageKey = 'explore-map-style';
            const availableStyles = {
                light: {
                    name: 'Light',
                    layer: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        noWrap: true,
                        bounds: null,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a> | &copy; <a href="https://carto.com/" target="_blank" rel="noreferrer">CARTO</a>',
                    }),
                },
                dark: {
                    name: 'Dark',
                    layer: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        noWrap: true,
                        bounds: null,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a> | &copy; <a href="https://carto.com/" target="_blank" rel="noreferrer">CARTO</a>',
                    }),
                },
                satellite: {
                    name: 'Satellite',
                    layer: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 19,
                        noWrap: true,
                        bounds: null,
                        attribution: '&copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community',
                    }),
                },
            };

            const egyptCenter = [26.8206, 30.8025];
            const egyptBounds = L.latLngBounds([
                [22.0, 24.7],
                [31.5, 34.3],
            ]);
            const map = L.map(mapElement, {
                scrollWheelZoom: false,
                zoomControl: true,
                maxBounds: egyptBounds,
                maxBoundsViscosity: 0.9,
                minZoom: 6,
                maxZoom: 18,
                locale: 'en',
            }).setView(egyptCenter, 6);

            let activeBaseLayer = null;

            function syncMapStyleButtons(activeStyle) {
                mapStyleButtons.forEach(function (button) {
                    const isActive = button.dataset.mapStyle === activeStyle;
                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
            }

            function setMapStyle(styleName, persist) {
                const style = availableStyles[styleName] || availableStyles.light;

                if (activeBaseLayer) {
                    map.removeLayer(activeBaseLayer);
                }

                activeBaseLayer = style.layer.addTo(map);
                map.setMaxBounds(egyptBounds);
                syncMapStyleButtons(styleName in availableStyles ? styleName : 'light');

                if (persist !== false) {
                    try {
                        localStorage.setItem(styleStorageKey, styleName in availableStyles ? styleName : 'light');
                    } catch (error) {}
                }
            }

            mapStyleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMapStyle(button.dataset.mapStyle || 'light', true);
                });
            });

            let savedMapStyle = 'light';

            try {
                savedMapStyle = localStorage.getItem(styleStorageKey) || 'light';
            } catch (error) {}

            setMapStyle(savedMapStyle, false);

            function escapeHtml(value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function createMarkerIcon(markerType, rating, isFavorited) {
                const normalizedType = ['natural', 'museum', 'historical'].includes(markerType) ? markerType : 'historical';
                const normalizedRatingBand = getRatingBand(rating);
                const favoriteClass = isFavorited ? ' attraction-marker--favorite' : '';
                const favoriteBadge = isFavorited ? '<span class="attraction-marker__favorite-badge" aria-hidden="true">★</span>' : '';

                return L.divIcon({
                    className: 'attraction-marker attraction-marker--' + normalizedType + ' attraction-marker--' + normalizedRatingBand + favoriteClass,
                    html: '<span class="attraction-marker__glyph" aria-hidden="true">' + (
                        normalizedType === 'natural' ? '🌿' : normalizedType === 'museum' ? '🏛' : '⛩'
                    ) + '</span>' + favoriteBadge,
                    iconSize: [34, 34],
                    iconAnchor: [17, 34],
                    popupAnchor: [0, -30],
                });
            }

            function buildMarkerTooltip(attraction) {
                const imageThumb = attraction.image
                    ? '<span class="attraction-marker-tooltip__thumb"><img src="' + escapeHtml(attraction.image) + '" alt="' + escapeHtml(attraction.name) + '" loading="lazy"></span>'
                    : '<span class="attraction-marker-tooltip__thumb attraction-marker-tooltip__thumb--placeholder" aria-hidden="true">⛳</span>';
                const ratingValue = Number(attraction.rating || 0).toFixed(1);
                const ratingBand = getRatingBand(attraction.rating);

                return (
                    '<div class="attraction-marker-tooltip__card">' +
                        imageThumb +
                        '<span class="attraction-marker-tooltip__body">' +
                            '<span class="attraction-marker-tooltip__name">' + escapeHtml(attraction.name) + '</span>' +
                            '<span class="attraction-marker-tooltip__meta">★ ' + ratingValue + ' · ' + escapeHtml(attraction.location || 'Egypt') + '</span>' +
                            '<span class="attraction-marker-tooltip__chip attraction-marker-tooltip__chip--' + ratingBand + '">' +
                                (ratingBand === 'rating-high' ? 'Highly rated' : ratingBand === 'rating-medium' ? 'Good rating' : 'Need more reviews') +
                            '</span>' +
                        '</span>' +
                    '</div>'
                );
            }

            function getAttractionTypeMeta(type) {
                switch (type) {
                    case 'beach':
                        return { label: 'Beach', icon: '🌊', className: 'is-beach' };
                    case 'coastal':
                        return { label: 'Coastal City', icon: '🏝️', className: 'is-coastal' };
                    default:
                        return { label: 'Historical', icon: '🏛️', className: 'is-historical' };
                }
            }

            function buildMarkerPopup(attraction) {
                const imageHtml = attraction.image
                    ? '<a class="cluster-popup__image cluster-popup__image--link" href="' + escapeHtml(attraction.url) + '"><img src="' + escapeHtml(attraction.image) + '" alt="' + escapeHtml(attraction.name) + '" loading="lazy"></a>'
                    : '';
                const ratingValue = Number(attraction.rating || 0).toFixed(1);
                const typeMeta = getAttractionTypeMeta(attraction.type);
                const location = escapeHtml(attraction.location || 'Egypt');
                const reviewsCount = Number(attraction.reviews_count || 0);
                const mapsQuery = encodeURIComponent(attraction.name + (attraction.location ? ', ' + attraction.location : ''));
                const routeUrl = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(attraction.lat + ',' + attraction.lng) + '&travelmode=driving';
                const directionsUrl = 'https://www.google.com/maps/search/?api=1&query=' + mapsQuery;

                return (
                    '<div class="cluster-popup cluster-popup--enhanced">' +
                        imageHtml +
                        '<div class="cluster-popup__content">' +
                            '<div class="cluster-popup__header">' +
                                '<span class="cluster-popup__type ' + typeMeta.className + '">' + typeMeta.icon + ' ' + typeMeta.label + '</span>' +
                                (attraction.is_favorited ? '<span class="cluster-popup__favorite">★ Saved</span>' : '') +
                            '</div>' +
                            '<h4>' + escapeHtml(attraction.name) + '</h4>' +
                            '<p class="cluster-popup__location">' + location + '</p>' +
                            '<div class="cluster-popup__stats">' +
                                '<span>★ ' + ratingValue + '</span>' +
                                '<span>' + reviewsCount + ' reviews</span>' +
                            '</div>' +
                            '<div class="cluster-popup__actions">' +
                                '<a href="' + escapeHtml(routeUrl) + '" class="btn btn-sm btn-primary" target="_blank" rel="noreferrer">Route</a>' +
                                '<a href="' + escapeHtml(directionsUrl) + '" class="btn btn-sm btn-outline" target="_blank" rel="noreferrer">Directions</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            }

            function getRatingBand(rating) {
                const numericRating = Number(rating || 0);

                if (numericRating >= 4.5) {
                    return 'rating-high';
                }

                if (numericRating >= 3.5) {
                    return 'rating-medium';
                }

                return 'rating-low';
            }

            const markerClusterGroup = L.markerClusterGroup({
                maxClusterRadius: 80,
                disableClusteringAtZoom: 10,
                spiderfyOnMaxZoom: true,
            });

            markerClusterGroup.on('clusterclick', function (event) {
                try {
                    const el = event.layer && event.layer._icon;
                    if (el) {
                        el.classList.add('cluster-marker--pulse');
                        setTimeout(function () {
                            el.classList.remove('cluster-marker--pulse');
                        }, 520);
                    }
                } catch (e) {}
            });

            markerClusterGroup.on('spiderfied', function (event) {
                try {
                    (event.markers || []).forEach(function (item, idx) {
                        const el = item && item.getElement && item.getElement();
                        if (el) {
                            el.classList.add('attraction-marker--enter');
                            el.style.animationDelay = Math.min(220, idx * 35) + 'ms';
                            setTimeout(function () {
                                el.classList.remove('attraction-marker--enter');
                                el.style.animationDelay = '';
                            }, 560 + Math.min(220, idx * 35));
                        }
                    });
                } catch (e) {}
            });

            const attractionMarkers = attractions.map(function (attraction) {
                const marker = L.marker([attraction.lat, attraction.lng], {
                    icon: createMarkerIcon(attraction.marker_type, attraction.rating, attraction.is_favorited),
                }).bindTooltip(
                    buildMarkerTooltip(attraction),
                    {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -18],
                        opacity: 0.96,
                        sticky: true,
                        className: 'attraction-marker-tooltip',
                    }
                ).bindPopup(buildMarkerPopup(attraction));

                // add entry animation when marker element is attached to DOM
                marker.on('add', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) {
                            el.setAttribute('tabindex', '0');
                            el.setAttribute('role', 'button');
                            el.setAttribute('aria-label', attraction.name + ' attraction marker');
                            // no immediate add; animation delay is applied during refreshMarkers
                            el.addEventListener('animationend', function () {
                                el.classList.remove('attraction-marker--enter');
                            }, { once: true });
                        }
                    } catch (e) {}
                });

                // hover effects
                marker.on('mouseover', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) el.classList.add('attraction-marker--hover');
                        if (marker.openTooltip) {
                            marker.openTooltip();
                        }
                    } catch (e) {}
                });

                marker.on('mouseout', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) el.classList.remove('attraction-marker--hover');
                        if (marker.closeTooltip) {
                            marker.closeTooltip();
                        }
                    } catch (e) {}
                });

                marker.on('focus', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) el.classList.add('attraction-marker--hover');
                        if (marker.openTooltip) {
                            marker.openTooltip();
                        }
                    } catch (e) {}
                });

                marker.on('blur', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) el.classList.remove('attraction-marker--hover');
                        if (marker.closeTooltip) {
                            marker.closeTooltip();
                        }
                    } catch (e) {}
                });

                // click/tap feedback (mobile-friendly)
                marker.on('click', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) {
                            el.classList.add('attraction-marker--tap');
                            setTimeout(function () { el.classList.remove('attraction-marker--tap'); }, 420);
                        }
                    } catch (e) {}
                });

                marker.on('touchstart', function () {
                    try {
                        const el = marker.getElement && marker.getElement();
                        if (el) {
                            el.classList.add('attraction-marker--tap');
                            setTimeout(function () { el.classList.remove('attraction-marker--tap'); }, 420);
                        }
                    } catch (e) {}
                });

                return {
                    id: attraction.id,
                    name: String(attraction.name || '').toLowerCase(),
                    originalName: attraction.name,
                    civilization_id: attraction.civilization_id,
                    region_id: attraction.region_id,
                    rating: Number(attraction.rating || 0),
                    ratingBand: getRatingBand(attraction.rating),
                    is_favorited: Boolean(attraction.is_favorited),
                    marker: marker,
                };
            });

            function setSearchStatus(message, isError) {
                if (!searchStatus) {
                    return;
                }

                searchStatus.textContent = message;
                searchStatus.dataset.state = isError ? 'error' : 'success';
            }

            function getActiveFilters() {
                return {
                    civilizationId: civilizationFilter ? civilizationFilter.value : '',
                    regionId: regionFilter ? regionFilter.value : '',
                    minRating: ratingFilter ? ratingFilter.value : '',
                };
            }

            function matchesFilters(attraction, filters) {
                if (filters.civilizationId && String(attraction.civilization_id) !== String(filters.civilizationId)) {
                    return false;
                }

                if (filters.regionId && String(attraction.region_id) !== String(filters.regionId)) {
                    return false;
                }

                if (filters.minRating && attraction.rating < Number(filters.minRating)) {
                    return false;
                }

                return true;
            }

            function getVisibleMarkers() {
                const filters = getActiveFilters();

                return attractionMarkers.filter(function (item) {
                    return matchesFilters(item, filters);
                });
            }

            function refreshMarkers(options) {
                const visibleMarkers = getVisibleMarkers();
                const shouldFit = !options || options.fit !== false;

                markerClusterGroup.clearLayers();

                visibleMarkers.forEach(function (item) {
                    markerClusterGroup.addLayer(item.marker);
                });

                // trigger staggered entry animation for markers that are actually in the DOM
                setTimeout(function () {
                    visibleMarkers.forEach(function (item, idx) {
                        try {
                            const el = item.marker && item.marker.getElement && item.marker.getElement();
                            if (el) {
                                // apply staggered delay
                                var delay = Math.min(720, idx * 85);
                                el.style.animationDelay = (delay) + 'ms';
                                el.classList.add('attraction-marker--enter');
                                // ensure cleanup after animation
                                setTimeout(function () {
                                    try { el.classList.remove('attraction-marker--enter'); el.style.animationDelay = ''; } catch(e){}
                                }, 640 + delay);
                            }
                        } catch (e) {}
                    });
                }, 120);

                if (visibleMarkers.length > 0) {
                    setSearchStatus('Showing ' + visibleMarkers.length + ' attractions on the map.', false);

                    if (shouldFit) {
                        const visibleBounds = L.latLngBounds(visibleMarkers.map(function (item) {
                            return item.marker.getLatLng();
                        }));

                        if (visibleBounds.isValid()) {
                            map.fitBounds(visibleBounds, {
                                padding: [24, 24],
                                maxZoom: 8,
                            });
                        }
                    }
                } else {
                    setSearchStatus('No attractions match the selected filters.', true);
                    map.setView(egyptCenter, 6);
                }
            }

            function findAttractionMatch(query) {
                const normalizedQuery = String(query || '').trim().toLowerCase();

                if (!normalizedQuery) {
                    return null;
                }

                const visibleMarkers = getVisibleMarkers();

                const exactMatch = visibleMarkers.find(function (item) {
                    return item.name === normalizedQuery;
                });

                if (exactMatch) {
                    return exactMatch;
                }

                return visibleMarkers.find(function (item) {
                    return item.name.includes(normalizedQuery);
                }) || null;
            }

            function focusOnAttraction(query) {
                const match = findAttractionMatch(query);

                if (!match) {
                    setSearchStatus('No attraction found in the current filters. Try a different name or clear filters.', true);
                    return;
                }

                setSearchStatus('Showing ' + match.originalName + ' on the map.', false);

                markerClusterGroup.zoomToShowLayer(match.marker, function () {
                    map.setView(match.marker.getLatLng(), Math.max(map.getZoom(), 10), {
                        animate: true,
                    });
                    match.marker.openPopup();
                });
            }

            if (searchButton && searchInput) {
                searchButton.addEventListener('click', function () {
                    focusOnAttraction(searchInput.value);
                });

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        focusOnAttraction(searchInput.value);
                    }
                });

                searchInput.addEventListener('input', function () {
                    if (!searchInput.value.trim()) {
                        setSearchStatus('', false);
                    }
                });
            }

            [civilizationFilter, regionFilter, ratingFilter].forEach(function (control) {
                if (!control) {
                    return;
                }

                control.addEventListener('change', function () {
                    refreshMarkers({ fit: true });
                });
            });

            if (clearFiltersButton) {
                clearFiltersButton.addEventListener('click', function () {
                    if (civilizationFilter) civilizationFilter.value = '';
                    if (regionFilter) regionFilter.value = '';
                    if (ratingFilter) ratingFilter.value = '';
                    setSearchStatus('', false);
                    refreshMarkers({ fit: true });
                });
            }

            map.addLayer(markerClusterGroup);
            refreshMarkers({ fit: true });
        });
    </script>
@endpush

<x-tourism-layout title="Explore Egypt | Explore">
    <section class="section-block page-top">
        <div class="container">
            <div class="section-head section-head-stack">
                <h1>Explore Attractions</h1>
                <p>Search, filter, and browse Egypt's destinations in one place.</p>
            </div>

            <div class="explore-map-section">
                @if($mapAttractions->isNotEmpty())
                    <div class="map-search-panel" role="search" aria-label="Search attractions on map">
                        <div class="map-search-panel__row">
                            <input
                                id="attractions-map-search"
                                type="search"
                                class="map-search-panel__input"
                                list="map-attraction-suggestions"
                                placeholder="Search attractions on the map"
                                aria-label="Search attractions on the map"
                            >
                            <button type="button" id="attractions-map-search-button" class="btn btn-primary map-search-panel__button">Find on Map</button>
                        </div>

                        <div class="map-search-panel__filters">
                            <select id="attractions-map-civilization-filter" class="map-search-panel__select" aria-label="Filter map by civilization">
                                <option value="">All civilizations</option>
                                @foreach($civilizations as $civilization)
                                    <option value="{{ $civilization->id }}">{{ $civilization->name }}</option>
                                @endforeach
                            </select>

                            <select id="attractions-map-region-filter" class="map-search-panel__select" aria-label="Filter map by region">
                                <option value="">All regions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>

                            <select id="attractions-map-rating-filter" class="map-search-panel__select" aria-label="Filter map by minimum rating">
                                <option value="">Any rating</option>
                                <option value="4.5">4.5 &amp; up</option>
                                <option value="4">4.0 &amp; up</option>
                                <option value="3">3.0 &amp; up</option>
                            </select>

                            <button type="button" id="attractions-map-clear-filters" class="btn btn-ghost map-search-panel__clear">Clear Filters</button>
                        </div>

                        <p id="attractions-map-search-status" class="map-search-panel__status" aria-live="polite"></p>
                        <div class="map-search-panel__legend" aria-label="Rating color legend">
                            <span class="map-search-panel__legend-item">
                                <span class="map-search-panel__legend-swatch map-search-panel__legend-swatch--high" aria-hidden="true"></span>
                                Highly rated
                            </span>
                            <span class="map-search-panel__legend-item">
                                <span class="map-search-panel__legend-swatch map-search-panel__legend-swatch--medium" aria-hidden="true"></span>
                                متوسط
                            </span>
                            <span class="map-search-panel__legend-item">
                                <span class="map-search-panel__legend-swatch map-search-panel__legend-swatch--low" aria-hidden="true"></span>
                                Low rated
                            </span>
                            <span class="map-search-panel__legend-item">
                                <span class="map-search-panel__legend-swatch map-search-panel__legend-swatch--favorite" aria-hidden="true"></span>
                                Favorited
                            </span>
                        </div>
                        <div class="map-style-switcher" aria-label="Map style switcher">
                            <button type="button" class="map-style-switcher__button" data-map-style="light" aria-pressed="false">Light</button>
                            <button type="button" class="map-style-switcher__button" data-map-style="dark" aria-pressed="false">Dark</button>
                            <button type="button" class="map-style-switcher__button" data-map-style="satellite" aria-pressed="false">Satellite</button>
                        </div>
                        <datalist id="map-attraction-suggestions">
                            @foreach($mapAttractions as $mapAttraction)
                                <option value="{{ $mapAttraction['name'] }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div id="attractions-map" class="attractions-map" aria-label="Interactive map of attractions"></div>
                    <script type="application/json" id="attractions-map-data">@json($mapAttractions)</script>
                @else
                    <div class="explore-map-empty">
                        <p>No attractions with map coordinates are available yet. Try browsing the cards below or check back after more destinations are added.</p>
                    </div>
                @endif
            </div>

            <form action="{{ route('explore') }}" method="GET" class="explore-filters">
                <div class="explore-type-tabs" role="tablist" aria-label="Attraction categories">
                    @php
                        $selectedType = $filters['type'] ?? '';
                    @endphp
                    <a href="{{ route('explore', request()->except('page', 'type')) }}" class="explore-type-tab {{ $selectedType === '' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === '' ? 'true' : 'false' }}">All</a>
                    <a href="{{ route('explore', array_merge(request()->except('page'), ['type' => 'historical'])) }}" class="explore-type-tab {{ $selectedType === 'historical' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === 'historical' ? 'true' : 'false' }}">🏛️ Historical</a>
                    <a href="{{ route('explore', array_merge(request()->except('page'), ['type' => 'beach'])) }}" class="explore-type-tab {{ $selectedType === 'beach' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === 'beach' ? 'true' : 'false' }}">🌊 Beaches</a>
                    <a href="{{ route('explore', array_merge(request()->except('page'), ['type' => 'coastal'])) }}" class="explore-type-tab {{ $selectedType === 'coastal' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === 'coastal' ? 'true' : 'false' }}">🏝️ Coastal Cities</a>
                </div>

                <input type="hidden" name="type" value="{{ $selectedType }}">
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

                <select name="min_rating" aria-label="Filter by minimum rating">
                    <option value="">Any rating</option>
                    <option value="4.5" @selected((string) ($filters['min_rating'] ?? '') === '4.5')>4.5 &amp; up</option>
                    <option value="4" @selected((string) ($filters['min_rating'] ?? '') === '4')>4.0 &amp; up</option>
                    <option value="3" @selected((string) ($filters['min_rating'] ?? '') === '3')>3.0 &amp; up</option>
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
                            @php
                                $typeBadge = match($attraction->type) {
                                    'beach' => ['🌊 Beach', 'category-badge--beach'],
                                    'coastal' => ['🏝️ Coastal City', 'category-badge--coastal'],
                                    default => ['🏛️ Historical', 'category-badge--historical'],
                                };
                            @endphp
                            <span class="category-badge {{ $typeBadge[1] }}">{{ $typeBadge[0] }}</span>
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