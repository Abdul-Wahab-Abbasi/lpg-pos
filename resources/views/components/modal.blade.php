@props([
    'id',
    'title' => null,
    'icon' => null,
    'iconClass' => null,
    'size' => null,
])

<div {{ $attributes->merge(['class' => 'modal fade', 'id' => $id, 'tabindex' => '-1', 'aria-hidden' => 'true']) }}>
    <div class="modal-dialog modal-dialog-centered @if ($size) modal-{{ $size }} @endif">
        <div class="modal-content">
            @if ($title)
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($icon)
                            <i class="bi {{ $icon }} @if ($iconClass) {{ $iconClass }} @endif"></i>
                        @endif
                        {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif

            <div class="modal-body">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
