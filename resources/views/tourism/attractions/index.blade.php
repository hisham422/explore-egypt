@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.0/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.0/dist/MarkerCluster.Default.css">
    <style>
        .marker-cluster--activity { background-color: rgba(255, 152, 0, 0.95) !important; }
        .marker-cluster--beach { background-color: rgba(33, 150, 243, 0.95) !important; }
        .marker-cluster--coastal { background-color: rgba(38, 166, 154, 0.95) !important; }
        .marker-cluster--historical { background-color: rgba(218, 165, 32, 0.95) !important; }
        .marker-cluster .cluster-count { color: #fff; font-weight: 700; line-height: 1; text-align: center; }
        .marker-cluster { border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .cluster-count { padding: 4px 6px; font-size: 12px; }
        .map-search-panel__category-legend { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .map-search-panel__category-chip { display: inline-flex; align-items: center; gap: 6px; padding: 7px 10px; border-radius: 999px; background: #fff; color: #25324b; box-shadow: 0 1px 2px rgba(15,23,42,.08); border: 1px solid rgba(148,163,184,.35); font-size: 12px; }
        .map-search-panel__category-dot { width: 10px; height: 10px; border-radius: 999px; display: inline-block; }
        .map-search-panel__category-dot--historical { background: rgba(218,165,32,1); }
        .map-search-panel__category-dot--activity { background: rgba(255,152,0,1); }
        .map-search-panel__category-dot--beach { background: rgba(33,150,243,1); }
        .map-search-panel__category-dot--coastal { background: rgba(38,166,154,1); }
        .cluster-marker--pulse { animation: pulse-ring 520ms ease-out; }
        @keyframes pulse-ring { 0% { transform: scale(1); } 50% { transform: scale(1.12); } 100% { transform: scale(1); } }

        /* loading bar for map chunk progress */
        #map-loading-bar { transition: opacity 0.3s ease; }

        /* popup summary for cluster hover */
        .cluster-summary-popup .leaflet-popup-content-wrapper { padding: 8px 10px; border-radius: 6px; }
        .cluster-summary { font-size: 13px; line-height: 1.2; }
        .cluster-summary__dominant { margin-bottom: 4px; }
        .cluster-summary__breakdown { display:flex; gap:8px; flex-wrap:wrap; }
        .cluster-summary__item { color: #222; }
        .cluster-summary__item strong { color: #111; }
    </style>
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
            const resetViewButton = document.getElementById('attractions-map-reset-view');
            const visibleCountElement = document.getElementById('attractions-map-visible-count');
            const totalCountElement = document.getElementById('attractions-map-total-count');
            const mapStyleButtons = Array.from(document.querySelectorAll('[data-map-style]'));

            if (!mapElement || !dataElement || typeof window.L === 'undefined') {
                return;
            }

            const attractions = JSON.parse(dataElement.textContent || '[]');
            const totalAttractionsCount = attractions.length;
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
                const normalizedType = ['historical', 'activity', 'beach', 'coastal'].includes(markerType) ? markerType : 'historical';
                const normalizedRatingBand = getRatingBand(rating);
                const favoriteClass = isFavorited ? ' attraction-marker--favorite' : '';
                const favoriteBadge = isFavorited ? '<span class="attraction-marker__favorite-badge" aria-hidden="true">★</span>' : '';
                const glyphs = {
                    historical: '🏛️',
                    activity: '🎯',
                    beach: '🌊',
                    coastal: '🏝️',
                };

                return L.divIcon({
                    className: 'attraction-marker attraction-marker--' + normalizedType + ' attraction-marker--' + normalizedRatingBand + favoriteClass,
                    html: '<span class="attraction-marker__glyph" aria-hidden="true">' + glyphs[normalizedType] + '</span>' + favoriteBadge,
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
                    case 'activity':
                        return { label: 'Activity', icon: '🎯', className: 'is-activity' };
                    case 'beach':
                        return { label: 'Beach', icon: '🌊', className: 'is-beach' };
                    case 'coastal':
                        return { label: 'Coastal', icon: '🏝️', className: 'is-coastal' };
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

            // create loading bar element for chunkedLoading progress
            const mapLoadingBar = document.createElement('div');
            mapLoadingBar.id = 'map-loading-bar';
            mapLoadingBar.style.cssText = 'position:fixed;top:0;left:0;height:3px;background:linear-gradient(90deg,#4CAF50,#2196F3);width:0;z-index:1000;transition:width 0.2s ease;';
            document.body.appendChild(mapLoadingBar);

            const markerClusterGroup = L.markerClusterGroup({
                maxClusterRadius: 40,
                disableClusteringAtZoom: 12,
                spiderfyOnMaxZoom: true,
                chunkedLoading: true,
                animateAddingMarkers: false,
                chunkProgress: function (processed, total, elapsed) {
                    const pct = Math.round((processed / Math.max(total, 1)) * 100);
                    try { mapLoadingBar.style.width = pct + '%'; } catch (e) {}
                    if (pct >= 100) {
                        setTimeout(function () { try { mapLoadingBar.style.opacity = '0'; } catch (e) {} }, 200);
                    }
                },
                iconCreateFunction: function (cluster) {
                    const children = cluster.getAllChildMarkers() || [];
                    const counts = { historical: 0, activity: 0, beach: 0, coastal: 0 };

                    children.forEach(function (m) {
                        try {
                            const cls = (m.options && m.options.icon && m.options.icon.options && m.options.icon.options.className) || '';

                            if (cls.indexOf('attraction-marker--activity') !== -1) counts.activity++;
                            else if (cls.indexOf('attraction-marker--beach') !== -1) counts.beach++;
                            else if (cls.indexOf('attraction-marker--coastal') !== -1) counts.coastal++;
                            else counts.historical++;
                        } catch (e) {}
                    });

                    const dominant = Object.keys(counts).reduce(function (a, b) {
                        return counts[a] >= counts[b] ? a : b;
                    }, 'historical');

                    const childCount = cluster.getChildCount();
                    const size = (function () {
                        const base = 28;
                        const growth = Math.round(Math.log(childCount + 1) * 5);
                        const capped = Math.min(16, growth);
                        return base + capped;
                    })();

                    return L.divIcon({
                        html: '<div class="cluster-count">' + childCount + '</div>',
                        className: 'marker-cluster marker-cluster--' + dominant,
                        iconSize: L.point(size, size),
                    });
                }
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

            // cluster hover summary popup
            var _currentClusterPopup = null;

            markerClusterGroup.on('clustermouseover', function (event) {
                try {
                    const cluster = event.layer;
                    const children = cluster.getAllChildMarkers() || [];
                    const counts = { historical: 0, activity: 0, beach: 0, coastal: 0 };

                    children.forEach(function (m) {
                        try {
                            const cls = (m.options && m.options.icon && m.options.icon.options && m.options.icon.options.className) || '';
                            if (cls.indexOf('attraction-marker--activity') !== -1) counts.activity++;
                            else if (cls.indexOf('attraction-marker--beach') !== -1) counts.beach++;
                            else if (cls.indexOf('attraction-marker--coastal') !== -1) counts.coastal++;
                            else counts.historical++;
                        } catch (e) {}
                    });

                    const total = children.length;
                    const dominant = Object.keys(counts).reduce(function (a, b) { return counts[a] >= counts[b] ? a : b; }, 'historical');

                    const parts = Object.keys(counts).map(function (k) {
                        return '<span class="cluster-summary__item cluster-summary__item--' + k + '">' + k.charAt(0).toUpperCase() + k.slice(1) + ': <strong>' + counts[k] + '</strong></span>';
                    }).join(' ');

                    const html = '<div class="cluster-summary">' +
                        '<div class="cluster-summary__dominant">Dominant: <strong>' + dominant + '</strong> (' + total + ')</div>' +
                        '<div class="cluster-summary__breakdown">' + parts + '</div>' +
                        '</div>';

                    if (_currentClusterPopup) { try { map.closePopup(_currentClusterPopup); } catch (e) {} }
                    _currentClusterPopup = L.popup({ closeButton: false, autoPan: false, className: 'cluster-summary-popup' })
                        .setLatLng(event.latlng)
                        .setContent(html)
                        .openOn(map);
                } catch (e) {}
            });

            markerClusterGroup.on('clustermouseout', function (event) {
                try {
                    if (_currentClusterPopup) {
                        map.closePopup(_currentClusterPopup);
                        _currentClusterPopup = null;
                    }
                } catch (e) {}
            });

            const attractionMarkers = attractions.map(function (attraction) {
                const marker = L.marker([attraction.lat, attraction.lng], {
                    icon: createMarkerIcon(attraction.category || attraction.type, attraction.rating, attraction.is_favorited),
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

            function updateCountSummary(visibleCount) {
                if (visibleCountElement) {
                    visibleCountElement.textContent = String(visibleCount);
                }

                if (totalCountElement) {
                    totalCountElement.textContent = String(totalAttractionsCount);
                }
            }

            function resetMapView() {
                if (searchInput) {
                    searchInput.value = '';
                }

                if (civilizationFilter) civilizationFilter.value = '';
                if (regionFilter) regionFilter.value = '';
                if (ratingFilter) ratingFilter.value = '';

                setSearchStatus('Map view reset. Showing all available attractions.', false);
                refreshMarkers({ fit: true });
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

                try {
                    mapLoadingBar.style.opacity = '1';
                    mapLoadingBar.style.width = '0%';
                } catch (e) {}

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
                    setSearchStatus('Showing ' + visibleMarkers.length + ' of ' + totalAttractionsCount + ' attractions on the map.', false);
                    updateCountSummary(visibleMarkers.length);

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
                    updateCountSummary(0);
                    map.setView(egyptCenter, 6);
                }

                setTimeout(function () {
                    try {
                        mapLoadingBar.style.opacity = '0';
                    } catch (e) {}
                }, 300);
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

            if (resetViewButton) {
                resetViewButton.addEventListener('click', resetMapView);
            }

            map.addLayer(markerClusterGroup);
            refreshMarkers({ fit: true });

            window.addEventListener('resize', function () {
                map.invalidateSize();
            });
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
                        <div class="map-search-panel__summary" aria-label="Map attraction summary">
                            <span class="map-search-panel__summary-item">Visible <strong id="attractions-map-visible-count">0</strong></span>
                            <span class="map-search-panel__summary-divider" aria-hidden="true">/</span>
                            <span class="map-search-panel__summary-item">Total <strong id="attractions-map-total-count">{{ $mapAttractions->count() }}</strong></span>
                            <button type="button" id="attractions-map-reset-view" class="btn btn-ghost map-search-panel__reset">Reset View</button>
                        </div>
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
                        <div class="map-search-panel__category-legend" aria-label="Category legend">
                            <span class="map-search-panel__category-chip"><span class="map-search-panel__category-dot map-search-panel__category-dot--historical" aria-hidden="true"></span>Historical</span>
                            <span class="map-search-panel__category-chip"><span class="map-search-panel__category-dot map-search-panel__category-dot--activity" aria-hidden="true"></span>Activities</span>
                            <span class="map-search-panel__category-chip"><span class="map-search-panel__category-dot map-search-panel__category-dot--beach" aria-hidden="true"></span>Beaches</span>
                            <span class="map-search-panel__category-chip"><span class="map-search-panel__category-dot map-search-panel__category-dot--coastal" aria-hidden="true"></span>Coastal</span>
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
                    <a href="{{ route('explore', array_merge(request()->except('page'), ['type' => 'activity'])) }}" class="explore-type-tab {{ $selectedType === 'activity' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === 'activity' ? 'true' : 'false' }}">🎯 Activities</a>
                    <a href="{{ route('explore', array_merge(request()->except('page'), ['type' => 'beach'])) }}" class="explore-type-tab {{ $selectedType === 'beach' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $selectedType === 'beach' ? 'true' : 'false' }}">🌊 Beaches</a>
                </div>

                <input type="hidden" name="type" value="{{ $selectedType }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search places, civilizations, or regions" aria-label="Search attractions">

                <select name="civilization_id" aria-label="Filter by civilization">
                    <option value="">All civilizations</option>
                    @foreach($civilizations as $civilization)
                        <option value="{{ $civilization->id }}" @selected((int) ($filters['civilization_id'] ?? 0) === (int) $civilization->id)>{{ $civilization->name }}</option>
                    @endforeach
                </select>

                <select name="region_id" aria-label="Filter by governorate">
                    <option value="">All governorates</option>
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
                            <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" width="900" height="560" />
                            @php
                                $typeBadge = match($attraction->type) {
                                    'activity' => ['🎯 Activity', 'category-badge--activity'],
                                    'beach' => ['🌊 Beach', 'category-badge--beach'],
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