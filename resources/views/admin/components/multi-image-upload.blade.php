@props([
    'name' => 'images',
    'label' => 'Gallery Images',
    'help' => 'Select one or more files or drag and drop them here.',
    'accept' => 'image/*',
    'maxFileSizeMb' => 10,
])

@php
    $uid = $attributes->get('id') ?? $name.'-upload';
    $fieldName = str_ends_with($name, '[]') ? $name : $name.'[]';
@endphp

<div
    class="image-upload image-upload--multi"
    x-data="adminMultiImageUpload({
        accept: @js($accept),
        fieldName: @js($fieldName),
        maxFileSize: {{ (int) $maxFileSizeMb }} * 1024 * 1024,
    })"
    x-init="init()"
    x-on:dragover.prevent="handleDragOver"
    x-on:dragleave.prevent="handleDragLeave"
    x-on:drop.prevent="handleDrop"
>
    <label class="image-upload__label" for="{{ $uid }}">{{ $label }}</label>

    <input
        id="{{ $uid }}"
        class="image-upload__input"
        type="file"
        name="{{ $fieldName }}"
        accept="{{ $accept }}"
        multiple
        x-ref="input"
        x-on:change="handleChange"
    >

    <div
        class="image-upload__dropzone image-upload__dropzone--multi"
        role="button"
        tabindex="0"
        x-on:click="openPicker()"
        x-on:keydown.enter.prevent="openPicker()"
        x-on:keydown.space.prevent="openPicker()"
        :class="{ 'is-dragover': isDragging, 'has-preview': previews.length }"
    >
        <div class="image-upload__placeholder" x-show="!previews.length">
            <span class="image-upload__icon" aria-hidden="true">⇪</span>
            <strong>Drop multiple images or click to browse</strong>
            <span>Files stay in this selection until you remove them.</span>
        </div>

        <div class="image-upload__multi-preview" x-show="previews.length" x-cloak>
            <template x-for="(preview, index) in previews" :key="preview.id">
                <figure
                    class="image-upload__thumb"
                    draggable="true"
                    x-on:dragstart="startReorder(index)"
                    x-on:dragover.prevent="handleReorderOver(index)"
                    x-on:drop.prevent="dropReorder(index)"
                    x-on:dragend="finishReorder()"
                    :class="{ 'is-dragging': dragIndex === index }"
                >
                    <div class="image-upload__thumb-media" :class="{ 'image-upload__thumb-media--video': preview.type && preview.type.startsWith('video') }">
                        <template x-if="preview.type && preview.type.startsWith('video')">
                            <video :src="preview.url" muted playsinline preload="metadata"></video>
                        </template>
                        <template x-if="!preview.type || !preview.type.startsWith('video')">
                            <img :src="preview.url" :alt="preview.name">
                        </template>

                        <button
                            type="button"
                            class="image-upload__thumb-remove"
                            x-on:click.stop="removeAt(index)"
                            aria-label="Remove file"
                        >×</button>

                        <span class="image-upload__thumb-type" x-text="preview.type && preview.type.startsWith('video') ? 'Video' : 'Image'"></span>
                    </div>

                    <div class="image-upload__progress-wrap">
                        <div class="image-upload__progress-track" aria-hidden="true">
                            <span class="image-upload__progress-fill" :style="`width: ${preview.progress}%`"></span>
                        </div>
                        <div class="image-upload__progress-meta">
                            <figcaption :title="preview.name" x-text="preview.name"></figcaption>
                            <span x-text="`${preview.progress}%`"></span>
                        </div>
                    </div>
                </figure>
            </template>
        </div>
    </div>

    <div class="image-upload__meta">
        <div>
            <p class="image-upload__filename" x-text="summaryText"></p>
            <p class="admin-help" style="margin:4px 0 0;">{{ $help }}</p>
        </div>

        <button type="button" class="admin-btn admin-btn-muted image-upload__reset" x-show="previews.length" x-on:click="resetSelection()">Clear All</button>
    </div>

    <p class="image-upload__error" x-show="errorMessage" x-text="errorMessage" x-cloak></p>
    <p class="image-upload__error image-upload__error--submit" x-show="submitError" x-text="submitError" x-cloak></p>

    <div class="image-upload__submit-status" x-show="isUploading" x-cloak>
        <div class="image-upload__submit-status-bar">
            <span class="image-upload__submit-status-bar-fill" :style="`width: ${uploadProgress}%`"></span>
        </div>
        <p x-text="`Uploading media... ${uploadProgress}%`"></p>
    </div>
</div>
