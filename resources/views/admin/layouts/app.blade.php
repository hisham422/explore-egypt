<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --admin-bg: #f5f7fb;
            --admin-panel: #ffffff;
            --admin-border: #dfe5ef;
            --admin-text: #1a2433;
            --admin-muted: #617086;
            --admin-primary: #1f3d63;
            --admin-contrast: #ffffff;
            --admin-shadow: 0 8px 26px rgba(22, 37, 58, 0.08);
        }

        body.admin-body.dark {
            --admin-bg: #0d131b;
            --admin-panel: #151c27;
            --admin-border: #263244;
            --admin-text: #e5e7eb;
            --admin-muted: #9ca3af;
            --admin-primary: #82aee0;
            --admin-contrast: #f8fafc;
            --admin-shadow: 0 12px 30px rgba(0, 0, 0, 0.38);
        }

        body.admin-body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--admin-text);
            background: var(--admin-bg);
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: var(--admin-panel);
            border-right: 1px solid var(--admin-border);
            padding: 20px 14px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-brand {
            margin: 0 8px 16px;
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--admin-primary);
        }

        .admin-nav {
            display: grid;
            gap: 6px;
        }

        .admin-nav a {
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--admin-text);
            text-decoration: none;
            font-size: 0.92rem;
            border: 1px solid transparent;
            transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .admin-nav a:hover {
            background: rgba(130, 174, 224, 0.12);
            border-color: rgba(130, 174, 224, 0.22);
            transform: translateX(2px);
        }

        .admin-nav a.is-active {
            background: rgba(130, 174, 224, 0.18);
            border-color: rgba(130, 174, 224, 0.3);
            color: var(--admin-primary);
            font-weight: 600;
        }

        .admin-main {
            min-width: 0;
        }

        .admin-topbar {
            background: var(--admin-panel);
            border-bottom: 1px solid var(--admin-border);
            padding: 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .admin-topbar h1 {
            margin: 0;
            font-size: 1.2rem;
            line-height: 1.3;
            color: var(--admin-primary);
        }

        .admin-topbar .meta {
            margin: 2px 0 0;
            color: var(--admin-muted);
            font-size: 0.86rem;
        }

        .admin-content {
            padding: 20px 22px 28px;
        }

        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .admin-card {
            background: var(--admin-panel);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            box-shadow: var(--admin-shadow);
            padding: 16px;
        }

        .admin-card-empty {
            text-align: center;
            padding: 26px;
            border: 1px dashed var(--admin-border);
            border-radius: 12px;
            background: var(--admin-panel);
        }

        .admin-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .admin-stat h2 {
            margin: 0;
            font-size: 1.4rem;
            color: var(--admin-primary);
        }

        .admin-stat p {
            margin: 6px 0 0;
            color: var(--admin-muted);
            font-size: 0.88rem;
        }

        .admin-section-title {
            margin: 0 0 10px;
            color: var(--admin-primary);
            font-size: 1.05rem;
        }

        .admin-list {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .admin-list li {
            font-size: 0.9rem;
            line-height: 1.45;
        }

        .admin-empty {
            margin: 0;
        }

        .admin-table-wrap {
            overflow-x: auto;
            background: var(--admin-panel);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .admin-table th,
        .admin-table td {
            padding: 11px 12px;
            text-align: left;
            border-bottom: 1px solid var(--admin-border);
            vertical-align: top;
        }

        .admin-table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--admin-muted);
            font-weight: 700;
            background: var(--admin-panel);
        }

        .admin-table tr:last-child td {
            border-bottom: 0;
        }

        .admin-form-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr 1fr;
        }

        .admin-form-grid .full {
            grid-column: 1 / -1;
        }

        .admin-field label {
            display: block;
            font-size: 0.88rem;
            margin-bottom: 6px;
            color: var(--admin-text);
            font-weight: 600;
        }

        .admin-field input,
        .admin-field textarea,
        .admin-field select {
            width: 100%;
            border: 1px solid var(--admin-border);
            border-radius: 10px;
            padding: 10px 12px;
            font: inherit;
            background: var(--admin-panel);
            color: var(--admin-text);
        }

        .admin-field textarea {
            min-height: 120px;
            resize: vertical;
        }

        [x-cloak] {
            display: none !important;
        }

        .image-upload {
            display: grid;
            gap: 10px;
        }

        .image-upload--banner .image-upload__dropzone {
            min-height: 280px;
            border-style: solid;
            background:
                linear-gradient(180deg, rgba(14, 25, 40, 0.14), rgba(14, 25, 40, 0.06)),
                linear-gradient(135deg, var(--admin-panel) 0%, color-mix(in srgb, var(--admin-panel) 92%, var(--admin-bg)) 100%);
        }

        .image-upload--banner .image-upload__dropzone.has-preview {
            min-height: 280px;
        }

        .image-upload--banner .image-upload__preview {
            min-height: 280px;
        }

        .image-upload--banner .image-upload__placeholder {
            max-width: 360px;
        }

        .image-upload--banner .image-upload__icon {
            width: 64px;
            height: 64px;
            font-size: 1.65rem;
        }

        .image-upload--banner .image-upload__placeholder strong {
            font-size: 1.02rem;
        }

        .image-upload__label {
            display: block;
            font-size: 0.88rem;
            margin-bottom: 2px;
            color: var(--admin-text);
            font-weight: 600;
        }

        .image-upload__input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .image-upload__dropzone {
            appearance: none;
            border: 1.5px dashed var(--admin-border);
            border-radius: 16px;
            background: linear-gradient(180deg, var(--admin-panel) 0%, color-mix(in srgb, var(--admin-panel) 92%, var(--admin-bg)) 100%);
            min-height: 220px;
            padding: 18px;
            display: grid;
            place-items: center;
            width: 100%;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .image-upload__dropzone:hover {
            transform: translateY(-1px);
            border-color: var(--admin-primary);
            box-shadow: 0 10px 22px rgba(31, 61, 99, 0.08);
        }

        .image-upload__dropzone:focus-visible {
            outline: 2px solid #2d527f;
            outline-offset: 3px;
        }

        .image-upload__dropzone.is-dragover {
            border-color: var(--admin-primary);
            background: linear-gradient(180deg, color-mix(in srgb, var(--admin-panel) 88%, var(--admin-primary)) 0%, var(--admin-panel) 100%);
            box-shadow: 0 12px 26px rgba(31, 61, 99, 0.12);
        }

        .image-upload__dropzone.has-preview {
            padding: 0;
            overflow: hidden;
        }

        .image-upload__dropzone--multi {
            min-height: 180px;
            padding: 14px;
            align-items: stretch;
            place-items: stretch;
        }

        .image-upload__dropzone--multi.has-preview {
            padding: 14px;
        }

        .image-upload__multi-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
            width: 100%;
        }

        .image-upload__thumb {
            margin: 0;
            padding: 10px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(184, 199, 218, 0.95);
            box-shadow: 0 8px 18px rgba(31, 61, 99, 0.08);
            display: grid;
            gap: 10px;
            min-width: 0;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .image-upload__thumb:hover {
            transform: translateY(-1px);
            border-color: #8ea9c9;
            box-shadow: 0 12px 24px rgba(31, 61, 99, 0.12);
        }

        .image-upload__thumb.is-dragging {
            opacity: 0.68;
            transform: scale(0.98);
        }

        .image-upload__thumb-media {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            border-radius: 10px;
            background: linear-gradient(180deg, #0f1721 0%, #162131 100%);
        }

        .image-upload__thumb-media img,
        .image-upload__thumb-media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-upload__thumb-media--video img,
        .image-upload__thumb-media--video video {
            filter: saturate(0.9) contrast(0.98);
        }

        .image-upload__thumb-type {
            position: absolute;
            left: 10px;
            top: 10px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(10, 18, 30, 0.74);
            color: var(--admin-contrast);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .image-upload__thumb-remove {
            position: absolute;
            right: 10px;
            top: 10px;
            width: 30px;
            height: 30px;
            border: 0;
            border-radius: 999px;
            background: rgba(10, 18, 30, 0.78);
            color: var(--admin-contrast);
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
        }

        .image-upload__thumb-remove:hover {
            background: #b03a3a;
        }

        .image-upload__progress-wrap {
            display: grid;
            gap: 6px;
        }

        .image-upload__progress-track,
        .image-upload__submit-status-bar {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            background: #e5ecf5;
        }

        .image-upload__progress-fill,
        .image-upload__submit-status-bar-fill {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #2d527f 0%, #64a0e0 100%);
            transition: width 0.18s ease;
        }

        .image-upload__progress-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #30445f;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .image-upload__progress-meta figcaption {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .image-upload__submit-status {
            display: grid;
            gap: 6px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #eef5ff;
            color: #28415f;
            border: 1px solid #c8d8ee;
        }

        .image-upload__submit-status p {
            margin: 0;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .image-upload__error--submit {
            color: #9d2e2e;
        }

        .image-upload__preview {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 220px;
            object-fit: cover;
        }

        .image-upload__placeholder {
            display: grid;
            justify-items: center;
            gap: 6px;
            text-align: center;
            color: #51657f;
            max-width: 260px;
        }

        .image-upload__icon {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            background: #e8eef6;
            color: #1f3d63;
            font-size: 1.4rem;
            box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.9);
        }

        .image-upload__placeholder strong {
            color: #1f2f43;
            font-size: 0.98rem;
        }

        .image-upload__placeholder span {
            font-size: 0.85rem;
            line-height: 1.45;
        }

        .image-upload__multi-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            width: 100%;
        }

        .image-upload__thumb {
            position: relative;
            margin: 0;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
            background: var(--admin-panel);
            cursor: grab;
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
        }

        .image-upload__thumb:hover {
            transform: translateY(-1px);
            border-color: #9cb1cc;
            box-shadow: 0 8px 14px rgba(25, 43, 71, 0.08);
        }

        .image-upload__thumb.is-dragging {
            opacity: 0.72;
            cursor: grabbing;
            transform: scale(0.98);
        }

        .image-upload__thumb img {
            width: 100%;
            height: 78px;
            object-fit: cover;
            display: block;
        }

        .image-upload__thumb figcaption {
            font-size: 0.74rem;
            color: var(--admin-text);
            padding: 6px 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-top: 1px solid var(--admin-border);
            background: var(--admin-panel);
        }

        .image-upload__thumb-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 0;
            background: rgba(17, 30, 50, 0.76);
            color: var(--admin-contrast);
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            display: grid;
            place-items: center;
        }

        .image-upload__thumb-remove:hover {
            background: rgba(17, 30, 50, 0.95);
        }

        .attraction-gallery-admin__item {
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease, opacity 0.16s ease;
        }

        .attraction-gallery-admin__item.is-dragging {
            opacity: 0.7;
            transform: scale(0.98);
            box-shadow: 0 12px 22px rgba(18, 35, 59, 0.12);
            border-color: #97acc6;
        }

        .attraction-gallery-admin__item.is-drop-target {
            border-color: #1f3d63;
            box-shadow: 0 0 0 3px rgba(31, 61, 99, 0.1);
        }

        .image-upload__meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .image-upload__filename {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--admin-text);
            word-break: break-word;
        }

        .image-upload__error {
            margin: 0;
            color: #b03a3a;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .image-upload__reset {
            flex-shrink: 0;
        }

        .image-upload__preview-wrap {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .image-upload__debug {
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 10px;
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(10, 18, 30, 0.78);
            color: #f4f7fb;
            font-size: 0.76rem;
            line-height: 1.35;
            word-break: break-all;
            pointer-events: none;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .image-upload__dropzone:hover .image-upload__debug,
        .image-upload__dropzone.is-dragover .image-upload__debug {
            opacity: 1;
            transform: translateY(0);
        }

        .admin-field input:focus,
        .admin-field textarea:focus,
        .admin-field select:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(130, 174, 224, 0.14);
        }

        .admin-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .admin-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 10px;
            border: 1px solid transparent;
            padding: 9px 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        .admin-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(17, 36, 63, 0.12);
        }

        .admin-btn:active {
            transform: translateY(0);
        }

        .admin-btn-primary {
            background: var(--admin-primary);
            border-color: var(--admin-primary);
            color: var(--admin-contrast);
        }

        .admin-btn-muted {
            background: var(--admin-panel);
            border-color: var(--admin-border);
            color: var(--admin-text);
        }

        .admin-btn-danger {
            background: rgba(221, 117, 117, 0.12);
            border-color: rgba(221, 117, 117, 0.26);
            color: #b95f5f;
        }

        .admin-actions-inline {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .admin-alert {
            margin-bottom: 14px;
            border: 1px solid rgba(117, 187, 128, 0.3);
            background: rgba(117, 187, 128, 0.12);
            color: #8ed29c;
            padding: 10px 12px;
            border-radius: 10px;
        }

        .admin-alert-error {
            border-color: rgba(221, 117, 117, 0.28);
            background: rgba(221, 117, 117, 0.12);
            color: #f0a8a8;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .admin-badge-admin {
            background: rgba(130, 174, 224, 0.18);
            color: var(--admin-primary);
        }

        .admin-badge-user {
            background: rgba(148, 163, 184, 0.14);
            color: var(--admin-muted);
        }

        .admin-search {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .admin-search input {
            width: min(320px, 100%);
            border: 1px solid var(--admin-border);
            border-radius: 10px;
            padding: 9px 11px;
            font: inherit;
            background: var(--admin-panel);
            color: var(--admin-text);
        }

        .admin-error {
            margin-top: 4px;
            font-size: 0.82rem;
            color: #b03a3a;
        }

        .admin-help {
            color: var(--admin-muted);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .admin-form-card__title {
            margin: 0 0 4px;
            color: var(--admin-primary);
            font-size: 1.06rem;
        }

        .admin-form-card__description,
        .admin-gallery-help,
        .admin-gallery-caption {
            margin: 0;
        }

        .admin-gallery-help {
            margin: 0 0 10px;
        }

        .attraction-gallery-admin__item {
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px;
            padding: 8px;
            background: var(--admin-panel) !important;
        }

        .attraction-gallery-admin__media {
            width: 100%;
            height: 92px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            position: relative;
            background: var(--admin-bg);
            overflow: hidden;
        }

        .attraction-gallery-admin__media--video {
            background: #0b1016;
        }

        .attraction-gallery-admin__icon {
            width: 32px;
            height: 32px;
            color: var(--admin-contrast);
        }

        .attraction-gallery-admin__image {
            width: 100%;
            height: 92px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
            margin-bottom: 8px;
        }

        .admin-theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--admin-border);
            background: var(--admin-panel);
            color: var(--admin-text);
            border-radius: 10px;
            padding: 9px 13px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        .admin-theme-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(17, 36, 63, 0.12);
        }

        body.admin-body.dark .admin-table-wrap {
            background: var(--admin-panel);
        }

        body.admin-body.dark .admin-table th {
            background: rgba(255, 255, 255, 0.03);
        }

        body.admin-body.dark .admin-table th,
        body.admin-body.dark .admin-table td {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        body.admin-body.dark .admin-card-empty,
        body.admin-body.dark .admin-btn-muted,
        body.admin-body.dark .admin-search input,
        body.admin-body.dark .image-upload__dropzone,
        body.admin-body.dark .image-upload__thumb,
        body.admin-body.dark .image-upload__submit-status,
        body.admin-body.dark .image-upload__thumb figcaption {
            background: var(--admin-panel);
            color: var(--admin-text);
        }

        body.admin-body.dark .admin-card-empty,
        body.admin-body.dark .admin-table-wrap,
        body.admin-body.dark .admin-btn-muted,
        body.admin-body.dark .admin-search input,
        body.admin-body.dark .admin-field input,
        body.admin-body.dark .admin-field textarea,
        body.admin-body.dark .admin-field select,
        body.admin-body.dark .image-upload__dropzone,
        body.admin-body.dark .image-upload__thumb,
        body.admin-body.dark .image-upload__submit-status {
            border-color: var(--admin-border);
        }

        body.admin-body.dark .admin-nav a:hover,
        body.admin-body.dark .admin-nav a.is-active {
            border-color: rgba(130, 174, 224, 0.24);
        }

        body.admin-body.dark .admin-btn-muted {
            color: var(--admin-text);
        }

        body.admin-body.dark .admin-btn-danger {
            background: rgba(221, 117, 117, 0.1);
            border-color: rgba(221, 117, 117, 0.2);
            color: #f0a8a8;
        }

        body.admin-body.dark .image-upload__dropzone {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        body.admin-body.dark .image-upload__dropzone:hover {
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
        }

        body.admin-body.dark .image-upload__thumb-media {
            background: linear-gradient(180deg, #0b1016 0%, #121a24 100%);
        }

        body.admin-body.dark .image-upload__thumb-type,
        body.admin-body.dark .image-upload__thumb-remove,
        body.admin-body.dark .image-upload__debug {
            background: rgba(0, 0, 0, 0.7);
        }

        body.admin-body.dark .image-upload__progress-track,
        body.admin-body.dark .image-upload__submit-status-bar {
            background: rgba(255, 255, 255, 0.08);
        }

        body.admin-body.dark .image-upload__progress-meta,
        body.admin-body.dark .image-upload__filename,
        body.admin-body.dark .image-upload__placeholder,
        body.admin-body.dark .image-upload__label,
        body.admin-body.dark .admin-field label {
            color: var(--admin-text);
        }

        body.admin-body.dark .image-upload__icon {
            background: rgba(130, 174, 224, 0.12);
            color: var(--admin-primary);
        }

        body.admin-body.dark .image-upload__progress-fill,
        body.admin-body.dark .image-upload__submit-status-bar-fill {
            background: linear-gradient(90deg, #5d88b7 0%, #8ab3e0 100%);
        }

        .admin-pagination {
            margin-top: 14px;
        }

        @media (max-width: 1160px) {
            .admin-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 960px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: static;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--admin-border);
            }

            .admin-nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .admin-content,
            .admin-topbar {
                padding-inline: 16px;
            }

            .admin-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .admin-nav {
                grid-template-columns: 1fr;
            }

            .admin-search input {
                width: 100%;
            }
        }
    </style>
</head>
<body class="admin-body light">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <p class="admin-brand">Tourism Admin</p>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.civilizations.index') }}" class="{{ request()->routeIs('admin.civilizations.*') ? 'is-active' : '' }}">Civilizations</a>
            <a href="{{ route('admin.civilization-periods.index') }}" class="{{ request()->routeIs('admin.civilization-periods.*') ? 'is-active' : '' }}">Periods</a>
            <a href="{{ route('admin.regions.index') }}" class="{{ request()->routeIs('admin.regions.*') ? 'is-active' : '' }}">Regions</a>
            <a href="{{ route('admin.attractions.index') }}" class="{{ request()->routeIs('admin.attractions.*') ? 'is-active' : '' }}">Attractions</a>
            <a href="{{ route('admin.appearance.edit') }}" class="{{ request()->routeIs('admin.appearance.*') ? 'is-active' : '' }}">Appearance</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">Users</a>
            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">Reviews</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>{{ $heading ?? 'Admin Panel' }}</h1>
                @isset($subheading)
                    <p class="meta">{{ $subheading }}</p>
                @endisset
            </div>
            <div class="admin-actions-inline">
                <button type="button" class="admin-theme-toggle" x-data x-on:click="window.globalThemeSwitcher?.toggle()" aria-label="Toggle admin theme">
                    <span aria-hidden="true">🌓</span>
                    <span>Theme</span>
                </button>
                <a href="{{ route('home') }}" class="admin-btn admin-btn-muted">View Site</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="admin-btn admin-btn-muted" type="submit">Logout</button>
                </form>
            </div>
        </header>

        <section class="admin-content">
            @if(session('status'))
                <div class="admin-alert">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="admin-alert admin-alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </section>
    </main>
</div>
</body>
</html>
