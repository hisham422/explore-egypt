@props([
    'name' => 'image',
    'label' => 'Image',
    'help' => 'Upload a file.',
    'currentUrl' => null,
    'currentLabel' => null,
    'previewSize' => '800x500',
    'accept' => '*/*',
    'variant' => 'default',
    'deleteUrl' => null,
])

@php
    $uid = $attributes->get('id') ?? $name.'-upload';
    $displayUrl = $currentUrl;
    $displayLabel = $currentLabel ?? $label;
    $placeholderUrl = \App\Support\ImageManager::placeholderUrl($displayLabel, $previewSize);
@endphp

<div
    class="image-upload image-upload--{{ $variant }}"
    x-data="adminImageUpload({
        name: '{{ $name }}',
        currentUrl: @js($displayUrl),
        placeholderUrl: @js($placeholderUrl),
        accept: @js($accept),
        deleteUrl: @js($deleteUrl),
    })"
    x-on:dragover.prevent="handleDragOver"
    x-on:dragleave.prevent="handleDragLeave"
    x-on:drop.prevent="handleDrop"
>
    <label class="image-upload__label" for="{{ $uid }}">{{ $label }}</label>

    <input
        id="{{ $uid }}"
        class="image-upload__input"
        type="file"
        name="{{ $name }}"
        accept="{{ $accept }}"
        x-ref="input"
        x-on:change="handleChange"
    >

    <button type="button" class="image-upload__dropzone" x-on:click="openPicker()" :class="{ 'is-dragover': isDragging, 'has-preview': previewUrl }">
        <template x-if="previewUrl">
            <div class="image-upload__preview-wrap">
                <img class="image-upload__preview" :src="previewUrl" :alt="fileName || '{{ $displayLabel }} preview'">
            </div>
        </template>

        <template x-if="!previewUrl">
            <div class="image-upload__placeholder">
                <span class="image-upload__icon" aria-hidden="true">⇪</span>
                <strong>{{ $variant === 'banner' ? 'Click or drag banner image to upload' : 'Click or drag image to upload' }}</strong>
                <span>{{ $variant === 'banner' ? 'Drop a banner file here or browse from your device.' : 'Drop a file here or browse from your device.' }}</span>
            </div>
        </template>
    </button>

    <div class="image-upload__meta">
        <div>
            <p class="image-upload__filename" x-text="fileName || 'No file selected yet'"></p>
            <p class="admin-help" style="margin:4px 0 0;">{{ $help }}</p>
        </div>

        <button type="button" class="admin-btn admin-btn-muted image-upload__reset" x-show="hasSelection" x-on:click="resetSelection()">Remove</button>
    </div>

    <p class="image-upload__error" x-show="errorMessage" x-text="errorMessage" x-cloak></p>

    @if($currentUrl)
        <p class="admin-help" style="margin:8px 0 0;">Current image will be replaced when a new file is chosen.</p>
    @endif
</div>
