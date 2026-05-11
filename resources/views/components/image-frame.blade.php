@props([
    'src' => null,
    'alt' => '',
    'label' => null,
    'placeholderSize' => '800x500',
    'class' => '',
    'loading' => 'lazy',
    'objectFit' => 'cover',
    'width' => null,
    'height' => null,
])

@php
    $fallbackLabel = $label ?: ($alt ?: 'Image');
    $placeholderUrl = \App\Support\ImageManager::placeholderUrl($fallbackLabel, $placeholderSize);
    $imageSrc = \App\Support\ImageManager::publicUrl($src, $fallbackLabel, $placeholderSize);
    $isDebug = app()->isLocal();
    $wrapperClass = trim('image-frame '.$class);
    
    // Extract dimensions from placeholderSize if not provided
    if (!$width && !$height && $placeholderSize) {
        [$w, $h] = explode('x', $placeholderSize);
        $width = (int) $w;
        $height = (int) $h;
    }
@endphp

<div class="{{ $wrapperClass }}" @if($isDebug) data-debug-image-frame @endif>
    <img
        src="{{ $imageSrc }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        class="image-frame__img"
        style="object-fit: {{ $objectFit }};"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($isDebug)
            data-debug-image-src="{{ $src }}"
            onerror="this.dataset.broken='1';this.src='{{ $placeholderUrl }}';this.closest('[data-debug-image-frame]')?.setAttribute('data-image-broken','1');this.alt='Broken image: {{ addslashes($fallbackLabel) }}';"
        @endif
    >

</div>
