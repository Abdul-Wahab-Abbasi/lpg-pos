@props([
    'variant' => 'success',
])

<div {{ $attributes->merge(['class' => "toast text-bg-{$variant}"]) }} role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">{{ $slot }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
