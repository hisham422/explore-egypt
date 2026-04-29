@props([
    'src' => null,
    'alt' => '',
    'label' => null,
    'placeholderSize' => '800x500',
    'class' => '',
    'loading' => 'lazy',
    'objectFit' => 'cover',
])

@php
    $fallbackLabel = $label ?: ($alt ?: 'Image');
    $placeholderUrl = \App\Support\ImageManager::placeholderUrl($fallbackLabel, $placeholderSize);
    $imageSrc = \App\Support\ImageManager::publicUrl($src, $fallbackLabel, $placeholderSize);
    $isDebug = app()->isLocal();
    $wrapperClass = trim('image-frame '.$class);
@endphp

<div class="{{ $wrapperClass }}" @if($isDebug) data-debug-image-frame @endif>
    <img
        src="{{ $imageSrc }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        class="image-frame__img"
        style="object-fit: {{ $objectFit }};"
        @if($isDebug)
            data-debug-image-src="{{ $src }}"
            onerror="this.dataset.broken='1';this.src='{{ $placeholderUrl }}';this.closest('[data-debug-image-frame]')?.setAttribute('data-image-broken','1');this.alt='Broken image: {{ addslashes($fallbackLabel) }}';"
        @endif
    >

</div>
