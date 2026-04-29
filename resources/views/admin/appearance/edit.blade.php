@extends('admin.layouts.app', [
    'title' => 'Admin | Appearance',
    'heading' => 'Appearance',
    'subheading' => 'Manage the homepage hero image',
])

@section('content')
    <div class="admin-card" style="max-width: 720px;">
        <h2 style="margin:0 0 6px;color:#1f3d63;">Homepage Hero Image</h2>
        <p class="admin-help" style="margin:0 0 16px;">Upload a file to use as the homepage banner. The file is stored in public storage and used on the home page.</p>

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

            <div class="admin-actions">
                <button type="submit" class="admin-btn admin-btn-primary">Save Hero Image</button>
            </div>
        </form>
    </div>
@endsection
