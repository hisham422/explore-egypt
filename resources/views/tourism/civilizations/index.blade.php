<x-tourism-layout title="Explore Egypt | Civilizations">
    <section class="section-block page-top">
        <div class="container">
            <div class="section-head section-head-stack">
                <h1>Civilizations</h1>
                <p>Explore Egypt's major historical eras and discover attractions connected to each one.</p>
            </div>

            <div class="grid cols-3">
                @forelse($civilizations as $civilization)
                    <a href="{{ route('civilizations.show', $civilization) }}" class="card card-link civilization-card">
                        <x-image-frame :src="$civilization->image" :alt="$civilization->name" :label="$civilization->name" placeholder-size="800x500" width="800" height="500" />
                        <span class="category-badge category-badge--civilization">Civilization</span>
                        <div class="card-content">
                            <h3>{{ $civilization->name }}</h3>
                            <p class="civilization-card-description">{{ \Illuminate\Support\Str::limit($civilization->description, 120) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">No civilizations found yet. Check back soon to start exploring Egypt's historical eras.</p>
                @endforelse
            </div>

            <div class="pagination-wrap">
                {{ $civilizations->links() }}
            </div>
        </div>
    </section>
</x-tourism-layout>
