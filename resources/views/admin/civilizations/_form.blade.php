@csrf

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $civilization->name) }}" required>
        @error('name')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        @include('admin.components.image-upload', [
            'name' => 'image',
            'label' => 'Image',
            'currentUrl' => $civilization->image ? $civilization->imageUrl('800x500') : null,
            'currentLabel' => $civilization->name ?: 'Civilization image',
            'previewSize' => '800x500',
        ])
        @error('image')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="hero_video_file">Hero Video Upload</label>
        <input
            id="hero_video_file"
            type="file"
            name="hero_video_file"
            accept="video/mp4,video/webm,video/ogg,video/quicktime"
        >
        <p class="admin-help">Upload a short optimized video for the civilization hero. MP4 or WebM works best.</p>
        @if ($civilization->hero_video_url)
            <p class="admin-help" style="margin:6px 0 0;">Current video: {{ $civilization->hero_video_url }}</p>
        @endif
        @error('hero_video_file')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label class="admin-check-row" for="hero_video_remove">
            <input
                id="hero_video_remove"
                type="checkbox"
                name="hero_video_remove"
                value="1"
                @checked((bool) old('hero_video_remove'))
            >
            <span>Remove current hero video</span>
        </label>
        <p class="admin-help">Check this to clear the uploaded or linked hero video and fall back to the civilization image.</p>
    </div>

    <div class="admin-field full">
        <label for="hero_video_url">Hero Video URL</label>
        <input
            id="hero_video_url"
            type="text"
            name="hero_video_url"
            value="{{ old('hero_video_url', $civilization->hero_video_url) }}"
            placeholder="https://example.com/civilization-video.mp4"
        >
        <p class="admin-help">Optional fallback. If you upload a file above, it will be used instead.</p>
        @error('hero_video_url')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ old('description', $civilization->description) }}</textarea>
        @error('description')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.civilizations.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save Civilization</button>
</div>
