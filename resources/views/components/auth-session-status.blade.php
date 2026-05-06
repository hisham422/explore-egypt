@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'theme-status font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
