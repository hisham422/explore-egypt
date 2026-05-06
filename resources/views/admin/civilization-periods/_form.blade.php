@csrf

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="civilization_id">Civilization</label>
        <select id="civilization_id" name="civilization_id" required>
            <option value="">Select a civilization</option>
            @foreach($civilizations as $civilization)
                <option value="{{ $civilization->id }}" @selected(old('civilization_id', $period->civilization_id) == $civilization->id)>{{ $civilization->name }}</option>
            @endforeach
        </select>
        @error('civilization_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="title">Title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $period->title) }}" required>
        @error('title')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="start_year">Start Year</label>
        <input id="start_year" type="number" name="start_year" value="{{ old('start_year', $period->start_year) }}" required>
        @error('start_year')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="end_year">End Year</label>
        <input id="end_year" type="number" name="end_year" value="{{ old('end_year', $period->end_year) }}" required>
        @error('end_year')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ old('description', $period->description) }}</textarea>
        @error('description')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="rulers">Rulers</label>
        <textarea id="rulers" name="rulers" placeholder="Optional, comma separated or short list">{{ old('rulers', $period->rulers) }}</textarea>
        @error('rulers')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="sort_order">Sort Order</label>
        <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $period->sort_order ?? 0) }}">
        @error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.civilization-periods.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save Period</button>
</div>