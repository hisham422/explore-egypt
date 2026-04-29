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
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ old('description', $civilization->description) }}</textarea>
        @error('description')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.civilizations.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save Civilization</button>
</div>
