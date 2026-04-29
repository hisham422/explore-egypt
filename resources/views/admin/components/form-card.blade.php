@php($title = $title ?? null)
@php($description = $description ?? null)

<div class="admin-card">
    @if($title)
        <h2 style="margin:0 0 4px;color:#1f3d63;font-size:1.06rem;">{{ $title }}</h2>
    @endif

    @if($description)
        <p class="admin-help" style="margin:0 0 12px;">{{ $description }}</p>
    @endif

    {{ $slot ?? '' }}
</div>
