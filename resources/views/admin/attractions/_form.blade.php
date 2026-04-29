@csrf

<div class="admin-form-grid">
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
        <label for="civilization_id">Civilization</label>
        <select id="civilization_id" name="civilization_id" required>
            <option value="">Select civilization</option>
            @foreach($civilizations as $civilization)
                <option value="{{ $civilization->id }}" @selected((int) old('civilization_id', $attraction->civilization_id) === (int) $civilization->id)>
                    {{ $civilization->name }}
                </option>
            @endforeach
        </select>
        @error('civilization_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="region_id">Region</label>
        <select id="region_id" name="region_id" required>
            <option value="">Select region</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}" @selected((int) old('region_id', $attraction->region_id) === (int) $region->id)>
                    {{ $region->name }}
                </option>
            @endforeach
        </select>
        @error('region_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
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
            <p class="admin-help" style="margin:0 0 10px;">Drag items to reorder the gallery, then drop to save. Images show thumbnails, videos show play icon.</p>
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
                        style="border:1px solid #d8e0ec;border-radius:12px;padding:8px;background:#fff;cursor:grab;"
                    >
                        @if($galleryItem->isVideo())
                            <div style="width:100%;height:92px;background:#000;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:8px;position:relative;">
                                <svg style="width:32px;height:32px;color:#fff;" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        @else
                            <img
                                src="{{ $galleryItem->imageUrl('320x220') }}"
                                alt="{{ $attraction->name }} gallery image"
                                style="width:100%;height:92px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;"
                                loading="lazy"
                            >
                        @endif
                        <p class="admin-help" style="margin:0 0 4px;">{{ $galleryItem->type === 'video' ? '🎥 Video' : '🖼️ Image' }} #{{ $index + 1 }}</p>
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
