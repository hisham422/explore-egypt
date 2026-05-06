@php($title = $title ?? null)
@php($description = $description ?? null)

<div class="admin-card">
    @if($title)
        <h2 class="admin-form-card__title">{{ $title }}</h2>
    @endif

    @if($description)
        <p class="admin-help admin-form-card__description">{{ $description }}</p>
    @endif

    {{ $slot ?? '' }}
</div>
