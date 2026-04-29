@csrf

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }}>
        @if($user->exists)
            <p class="admin-help">Leave empty to keep current password.</p>
        @endif
        @error('password')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" {{ $user->exists ? '' : 'required' }}>
    </div>

    <div class="admin-field full">
        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="user" @selected(old('role', $user->role ?? 'user') === 'user')>User</option>
            <option value="admin" @selected(old('role', $user->role ?? 'user') === 'admin')>Admin</option>
        </select>
        @error('role')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="admin-actions">
    <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-muted">Cancel</a>
    <button type="submit" class="admin-btn admin-btn-primary">Save User</button>
</div>
