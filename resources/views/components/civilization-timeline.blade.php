@props(['civilization'])

<div class="civilization-timeline-section">
    <div class="section-head section-head-stack">
        <h2>Historical Timeline</h2>
        <p>Explore the key periods and eras that shaped {{ $civilization->name }}.</p>
    </div>

    @if ($civilization->periods->count() > 0)
        <div class="timeline-container">
            <div class="timeline-line"></div>
            <div class="timeline-items">
                @foreach ($civilization->periods as $index => $period)
                    <div class="timeline-item" data-timeline-index="{{ $index }}">
                        <div class="timeline-marker">
                            <div class="timeline-dot"></div>
                        </div>

                        <details class="timeline-content timeline-card" {{ $index === 0 ? 'open' : '' }}>
                            <summary class="timeline-card-summary">
                                <div class="timeline-card-header">
                                    <h3 class="timeline-period-title">{{ $period->title }}</h3>
                                    <span class="timeline-period-dates">{{ $period->formatted_year_range }}</span>
                                </div>

                                <span class="timeline-expand-hint">
                                    <span class="timeline-expand-hint__text">View details</span>
                                    <span class="timeline-expand-hint__icon" aria-hidden="true">⌄</span>
                                </span>
                            </summary>

                            <div class="timeline-card-body">
                                <p class="timeline-description">{{ $period->description }}</p>

                                @if ($period->rulers)
                                    <div class="timeline-rulers">
                                        <strong>Notable Rulers:</strong>
                                        <p>{{ $period->rulers }}</p>
                                    </div>
                                @endif

                                @if ($period->attractions->isNotEmpty())
                                    <div class="timeline-key-attractions">
                                        <strong>Key Attractions</strong>
                                        <div class="timeline-attraction-links">
                                            @foreach ($period->attractions->take(3) as $attraction)
                                                <a href="{{ route('attractions.show', $attraction) }}" class="timeline-attraction-link">
                                                    {{ $attraction->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </details>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="empty-state">
            <p>No historical periods have been added yet for {{ $civilization->name }}. Check back soon!</p>
        </div>
    @endif
</div>
