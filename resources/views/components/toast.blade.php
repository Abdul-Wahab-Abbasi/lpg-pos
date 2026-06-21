@props([
    'variant' => 'success',
])

@php
    $icon = match ($variant) {
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'danger' => 'bi-x-circle-fill',
        default => 'bi-info-circle-fill',
    };
@endphp

<div {{ $attributes->merge(['class' => "toast toast-{$variant}"]) }} role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex align-items-center">
        <div class="toast-body d-flex align-items-center gap-2">
            <i class="bi {{ $icon }}"></i>
            <span>{{ $slot }}</span>
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
