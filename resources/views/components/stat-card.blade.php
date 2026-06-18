@props([
    'label',
    'value',
    'sub' => null,
    'icon' => null,
    'variant' => 'accent',
])

<div {{ $attributes->merge(['class' => "stat-card stat-card-{$variant}"]) }}>
    <div class="stat-card-label">{{ $label }}</div>
    <div class="stat-card-value">{{ $value }}</div>
    @if ($sub)
        <div class="stat-card-sub">{{ $sub }}</div>
    @endif
    @if ($icon)
        <i class="bi {{ $icon }} stat-card-icon"></i>
    @endif
</div>
