@extends('admin.layouts.app', [
    'title' => 'Admin | Users',
    'heading' => 'Users',
    'subheading' => 'Manage platform users and accounts',
])

@section('content')
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.users.index') }}" class="admin-search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name, email, or role">
            <button type="submit" class="admin-btn admin-btn-muted">Search</button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-muted">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn-primary">New User</a>
    </div>

    <p class="admin-help" style="margin:0 0 10px;">{{ $users->total() }} total records</p>

    @component('admin.components.table', ['headers' => ['ID', 'Name', 'Email', 'Role', 'Reviews', 'Favorites', 'Actions']])
        @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-admin' : 'admin-badge-user' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->reviews_count }}</td>
                <td>{{ $user->favorites_count }}</td>
                <td>
                    <div class="admin-actions-inline">
                        <a class="admin-btn admin-btn-muted" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        @if((int) auth()->id() === (int) $user->id)
                            <button class="admin-btn admin-btn-muted" type="button" disabled title="You cannot delete your own account.">Current User</button>
                        @else
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="admin-btn admin-btn-danger" type="submit">Delete</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">
                    <div class="admin-card-empty">
                        <p style="margin:0 0 8px;">No users found.</p>
                        <p class="admin-help" style="margin:0;">Try another search term, or create a new account.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    @endcomponent

    <div class="admin-pagination">{{ $users->links() }}</div>
@endsection
