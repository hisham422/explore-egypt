@extends('admin.layouts.app', [
    'title' => 'Admin | Appearance',
    'heading' => 'Appearance',
    'subheading' => 'Manage homepage hero media',
])

@section('content')
    <div class="admin-card" style="max-width: 720px;">
        <h2 style="margin:0 0 6px;color:#1f3d63;">Homepage Hero Media</h2>
        <p class="admin-help" style="margin:0 0 16px;">Control both fallback image and background video used in the homepage hero.</p>

        <form method="POST" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="admin-field">
                @include('admin.components.image-upload', [
                    'name' => 'hero_image',
                    'label' => 'Hero Image',
                    'help' => 'Upload a file to use as the homepage banner.',
                    'currentUrl' => $heroImage ? asset('storage/'.$heroImage) : null,
                    'currentLabel' => 'Homepage hero image',
                    'previewSize' => '1200x675',
                    'variant' => 'banner',
                ])
            </div>

            <div class="admin-field full">
                <label for="hero_video_file">Hero Video Upload</label>
                <input
                    id="hero_video_file"
                    type="file"
                    name="hero_video_file"
                    accept="video/mp4,video/webm,video/ogg,video/quicktime"
                >
                <p class="admin-help">Upload a short optimized video for the homepage hero. MP4 or WebM is recommended.</p>
                @if ($heroVideo)
                    <p class="admin-help" style="margin:6px 0 0;">Current video: {{ $heroVideo }}</p>
                @endif
                @error('hero_video_file')<p class="admin-error">{{ $message }}</p>@enderror
            </div>

            @if ($heroVideoUrl)
                <div class="admin-field full">
                    <label>Current Hero Video Preview</label>
                    <video
                        controls
                        muted
                        playsinline
                        preload="metadata"
                        style="width:100%;max-height:260px;border-radius:12px;border:1px solid #d7dee8;background:#0f1724;"
                    >
                        <source src="{{ $heroVideoUrl }}">
                        Your browser does not support the video tag.
                    </video>
                    <p class="admin-help" style="margin-top:8px;">Preview of the active homepage hero video.</p>
                </div>
            @endif

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
                <p class="admin-help">When enabled, the homepage hero falls back to the hero image only.</p>
            </div>

            <div class="admin-field full">
                <label for="hero_video_url">Hero Video URL or Storage Path</label>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input
                        id="hero_video_url"
                        type="text"
                        name="hero_video_url"
                        value="{{ old('hero_video_url', $heroVideo) }}"
                        placeholder="https://example.com/hero-video.mp4 or videos/hero/home-hero-video.mp4"
                    >
                    <button
                        type="button"
                        class="admin-btn admin-btn-muted"
                        style="white-space:nowrap;"
                        onclick="document.getElementById('hero_video_url').value = ''; document.getElementById('hero_video_url').focus();"
                    >
                        Clear URL
                    </button>
                </div>
                <p class="admin-help">Optional fallback. If a video file is uploaded above, that file is used.</p>
                @error('hero_video_url')<p class="admin-error">{{ $message }}</p>@enderror
            </div>

            <div class="admin-actions">
                <button type="submit" class="admin-btn admin-btn-primary">Save Hero Media</button>
            </div>
        </form>
    </div>
@endsection
