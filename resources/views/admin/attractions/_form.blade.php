@csrf

@php
    $periodOptionsByCivilization = $periods
        ->groupBy(fn ($period) => (string) $period->civilization_id)
        ->map(fn ($periodGroup) => $periodGroup->values()->map(function ($period) {
            return [
                'id' => (string) $period->id,
                'title' => $period->title,
                'label' => $period->title.' ('.$period->formatted_year_range.')',
            ];
        }));
@endphp

<div
    class="admin-form-grid"
    x-data="{
        civilizationId: @js((string) old('civilization_id', $attraction->civilization_id)),
        periodId: @js((string) old('civilization_period_id', $attraction->civilization_period_id)),
        periodOptionsByCivilization: @js($periodOptionsByCivilization),
        get availablePeriods() {
            return this.periodOptionsByCivilization[this.civilizationId] || [];
        },
        get periodStillValid() {
            return this.availablePeriods.some(period => period.id === this.periodId);
        },
    }"
    x-effect="if (!civilizationId || !periodStillValid) periodId = ''"
>
    <div class="admin-field">
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $attraction->name) }}" required>
        @error('name')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="location">Location</label>
        <input id="location" type="text" name="location" value="{{ old('location', $attraction->location) }}">
        @error('location')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="city">City</label>
        <input id="city" type="text" name="city" value="{{ old('city', $attraction->city) }}" placeholder="e.g. Cairo, Suez, Alexandria">
        @error('city')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="type">Attraction Type</label>
        <select id="type" name="type" required>
            <option value="historical" @selected(old('type', $attraction->type ?: 'historical') === 'historical')>Historical</option>
            <option value="activity" @selected(old('type', $attraction->type) === 'activity')>Activity</option>
            <option value="beach" @selected(old('type', $attraction->type) === 'beach')>Beach</option>
            <option value="coastal" @selected(old('type', $attraction->type) === 'coastal')>Coastal City</option>
        </select>
        @error('type')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="civilization_id">Civilization (optional)</label>
        <select id="civilization_id" name="civilization_id" x-model="civilizationId">
            <option value="">No civilization</option>
            @foreach($civilizations as $civilization)
                <option value="{{ $civilization->id }}" @selected((int) old('civilization_id', $attraction->civilization_id) === (int) $civilization->id)>
                    {{ $civilization->name }}
                </option>
            @endforeach
        </select>
        @error('civilization_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="region_id">Governorate / Region</label>
        <select id="region_id" name="region_id" required>
            <option value="">Select governorate</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}" @selected((int) old('region_id', $attraction->region_id) === (int) $region->id)>
                    {{ $region->name }}
                </option>
            @endforeach
        </select>
        @error('region_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="civilization_period_id">Historical Period</label>
        <select id="civilization_period_id" name="civilization_period_id" x-model="periodId">
            <option value="">Optional - link to a timeline period</option>
            <template x-for="period in availablePeriods" :key="period.id">
                <option :value="period.id" x-text="period.label"></option>
            </template>
        </select>
        <p class="admin-help" style="margin:6px 0 0;">Optional. Use this to connect the attraction to a specific historical period shown in the timeline.</p>
        <p class="admin-help" style="margin:6px 0 0;" x-show="civilizationId && availablePeriods.length === 0" x-cloak>No historical periods are available yet for the selected civilization.</p>
        @error('civilization_period_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <p class="admin-help" style="margin:0 0 10px;">Coordinates are required so the attraction can appear on the interactive map.</p>
        @include('admin.components.image-upload', [
            'name' => 'image',
            'label' => 'Image',
            'currentUrl' => $attraction->image ? $attraction->imageUrl('900x560') : null,
            'currentLabel' => $attraction->name ?: 'Attraction image',
            'previewSize' => '900x560',
            'deleteUrl' => $attraction->exists ? route('admin.attractions.main-image.destroy', $attraction) : null,
        ])
        @error('image')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        @include('admin.components.multi-image-upload', [
            'name' => 'media',
            'label' => 'Gallery Media (Images & Videos)',
            'help' => 'Upload images (jpg, png, webp) or videos (mp4). You can select multiple files at once.',
            'accept' => 'image/*,video/mp4',
        ])
        @php
            $maxUploads = (int) ini_get('max_file_uploads');
            $uploadMax = (string) ini_get('upload_max_filesize');
            $postMax = (string) ini_get('post_max_size');
        @endphp
        @if($maxUploads > 0 && $maxUploads <= 2)
            <div style="background:#fee; border:1px solid #f88; border-radius:8px; padding:12px; margin-top:8px; margin-bottom:8px;">
                <strong style="color:#c00;">⚠️ Server Limit Warning</strong>
                <p style="margin:6px 0 0; color:#333; font-size:0.9rem;">
                    Your server is configured to accept only <strong>{{ $maxUploads }} file{{ $maxUploads === 1 ? '' : 's' }}</strong> per upload request.
                    This prevents uploading multiple files at once. Contact your host or administrator to increase <code>max_file_uploads</code> in php.ini.
                </p>
            </div>
        @endif
        <p class="admin-help" style="margin-top:6px;">
            Server limits: up to {{ $maxUploads > 0 ? $maxUploads : 'many' }} files per request, max {{ $uploadMax ?: 'N/A' }} per file, {{ $postMax ?: 'N/A' }} per request.
        </p>
        @error('media')<p class="admin-error">{{ $message }}</p>@enderror
        @error('media.*')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    @if($attraction->exists && $attraction->images->isNotEmpty())
        <div class="admin-field full">
            <label>Current Gallery</label>
            <p class="admin-help admin-gallery-help">Drag items to reorder the gallery, then drop to save. Images show thumbnails, videos show play icon.</p>
            <div
                class="attraction-gallery-admin"
                data-attraction-gallery-admin
                data-save-url="{{ route('admin.attractions.images.reorder', $attraction) }}"
                style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;"
            >
                @foreach($attraction->images as $index => $galleryItem)
                    <div
                        class="attraction-gallery-admin__item"
                        draggable="true"
                        data-gallery-item
                        data-gallery-id="{{ $galleryItem->id }}"
                        style="cursor:grab;"
                    >
                        @if($galleryItem->isVideo())
                            <div class="attraction-gallery-admin__media attraction-gallery-admin__media--video">
                                <svg class="attraction-gallery-admin__icon" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        @else
                            <img
                                src="{{ $galleryItem->imageUrl('320x220') }}"
                                alt="{{ $attraction->name }} gallery image"
                                class="attraction-gallery-admin__image"
                                loading="lazy"
                            >
                        @endif
                        <p class="admin-help admin-gallery-caption">{{ $galleryItem->type === 'video' ? '🎥 Video' : '🖼️ Image' }} #{{ $index + 1 }}</p>
                        <button
                            type="button"
                            class="admin-btn admin-btn-danger"
                            style="width:100%;"
                            data-gallery-delete
                            data-delete-url="{{ route('admin.attractions.images.destroy', [$attraction, $galleryItem]) }}"
                            data-gallery-label="{{ $galleryItem->type === 'video' ? 'video' : 'image' }} #{{ $index + 1 }}"
                        >Delete</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="admin-field full">
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ old('description', $attraction->description) }}</textarea>
        @error('description')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.attractions.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save Attraction</button>
</div>
