<x-tourism-layout title="Explore Egypt | Regions">
    <section class="section-block page-top">
        <div class="container">
            <div class="section-head section-head-stack">
                <h1>Regions</h1>
                <p>Explore governorates and discover attractions by location.</p>
            </div>

            <div class="grid cols-4 region-grid">
                @forelse($regions as $region)
                    <a href="{{ route('regions.show', $region) }}" class="card card-link region-card">
                        <x-image-frame :src="$region->image" :alt="$region->name" :label="$region->name" placeholder-size="800x500" />
                        <span class="category-badge category-badge--region">Region</span>
                        <div class="card-content">
                            <h3>{{ $region->name }}</h3>
                            <p class="region-card-description">{{ \Illuminate\Support\Str::limit($region->description ?: 'Popular destination in Egypt.', 98) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty">No regions found yet. Check back soon to discover destinations across Egypt.</p>
                @endforelse
            </div>

            <div class="pagination-wrap">
                {{ $regions->links() }}
            </div>
        </div>
    </section>
</x-tourism-layout>
