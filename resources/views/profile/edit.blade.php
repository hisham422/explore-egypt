<x-tourism-layout title="Explore Egypt | Profile">
    <section class="profile-layout">
        <div class="container profile-grid">
            <aside class="profile-sidebar-card">
                <div class="profile-summary">
                    <img class="profile-avatar" src="{{ $user->avatarUrl('320x320') }}" alt="{{ $user->name }} avatar">
                    <p class="profile-eyebrow">Account</p>
                    <h2>{{ $user->name }}</h2>
                    <p class="muted">{{ $user->email }}</p>
                    <p class="profile-joined">Joined {{ optional($user->created_at)->format('M d, Y') }}</p>
                </div>

                <div class="profile-favorites-block">
                    <div class="section-head section-head-stack profile-section-head">
                        <h3 id="favorites">Favorites</h3>
                        <p>Quick access to the places you have already saved.</p>
                    </div>

                    @if($favorites->isEmpty())
                        <div class="profile-empty-state">
                            <p class="empty">Your favorites are empty right now.</p>
                            <p class="muted">Browse attractions and tap the heart icon to build your personal travel list.</p>
                        </div>
                    @else
                        <div class="favorites-grid favorites-grid--compact">
                            @foreach($favorites as $favorite)
                                @php($attraction = $favorite->attraction)
                                @if($attraction)
                                    <a href="{{ route('attractions.show', $attraction) }}" class="favorite-card card-link">
                                        <x-image-frame :src="$attraction->imageUrl('900x560')" :alt="$attraction->name" :label="$attraction->name" placeholder-size="900x560" />
                                        <div class="favorite-card-content">
                                            <h3>{{ $attraction->name }}</h3>
                                            <p>{{ $attraction->location }}</p>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </aside>

            <section class="settings-panel">
                <div class="settings-stack">
                    <div class="settings-card">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="settings-card">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="settings-card settings-card--danger">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </section>
        </div>
    </section>
</x-tourism-layout>
