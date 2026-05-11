@extends('admin.layouts.app', [
    'title' => 'Admin | Appearance',
    'heading' => 'Appearance',
    'subheading' => 'Manage homepage hero media',
])

@section('content')
    <div class="admin-card" style="max-width: 720px;">
        <h2 style="margin:0 0 6px;color:#1f3d63;">Homepage Hero Media</h2>
        <p class="admin-help" style="margin:0 0 16px;">Homepage hero media is now managed in the repository under <code>public/media/hero</code>.</p>

        <form method="POST" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="admin-field full">
                <label>Hero Image (Repository Managed)</label>
                <img
                    src="{{ asset('media/hero/home-hero.png') }}"
                    alt="Homepage hero image"
                    style="width:100%;max-height:260px;object-fit:cover;border-radius:12px;border:1px solid #d7dee8;background:#0f1724;"
                >
                <p class="admin-help" style="margin-top:8px;">Path: <code>public/media/hero/home-hero.png</code></p>
            </div>

            <div class="admin-field full">
                <label>Hero Video (Repository Managed)</label>
                <video
                    controls
                    muted
                    playsinline
                    preload="metadata"
                    style="width:100%;max-height:260px;border-radius:12px;border:1px solid #d7dee8;background:#0f1724;"
                >
                    <source src="{{ asset('media/hero/home-hero-video.mp4') }}">
                    Your browser does not support the video tag.
                </video>
                <p class="admin-help" style="margin-top:8px;">Path: <code>public/media/hero/home-hero-video.mp4</code></p>
                <p class="admin-help">Upload/remove URL fields are disabled because public pages now always use repository hero media.</p>
            </div>

            <div class="admin-actions">
                <span class="admin-help">No save action is required for homepage hero media.</span>
            </div>
        </form>
    </div>
@endsection
