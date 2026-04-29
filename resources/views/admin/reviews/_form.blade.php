@csrf

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="user_id">User</label>
        <select id="user_id" name="user_id" required>
            <option value="">Select user</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('user_id', $review->user_id) === (int) $user->id)>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
        @error('user_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="attraction_id">Attraction</label>
        <select id="attraction_id" name="attraction_id" required>
            <option value="">Select attraction</option>
            @foreach($attractions as $attraction)
                <option value="{{ $attraction->id }}" @selected((int) old('attraction_id', $review->attraction_id) === (int) $attraction->id)>
                    {{ $attraction->name }}
                </option>
            @endforeach
        </select>
        @error('attraction_id')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="rating">Rating (1-5)</label>
        <input id="rating" type="number" name="rating" min="1" max="5" value="{{ old('rating', $review->rating) }}" required>
        @error('rating')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field full">
        <label for="comment">Comment</label>
        <textarea id="comment" name="comment">{{ old('comment', $review->comment) }}</textarea>
        @error('comment')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save Review</button>
</div>
