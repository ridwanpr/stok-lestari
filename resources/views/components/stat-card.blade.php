@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'default',
])

@php
$valueColor = match ($tone) {
    'warning' => 'text-warning-700',
    'danger' => 'text-danger-700',
    'brand' => 'text-brand-800',
    default => 'text-ink-primary',
};
@endphp

<div class="rounded-xl border border-surface-border bg-surface-panel p-4 shadow-xs sm:p-5">
    <p class="text-xs font-semibold tracking-wider text-ink-muted uppercase">
        {{ $label }}
    </p>
    <p class="mt-2 flex items-baseline">
        <span class="text-3xl font-semibold tracking-tight tabular-nums {{ $valueColor }}">
            {{ $value }}
        </span>
    </p>
    @if ($hint)
        <p class="mt-1 text-xs text-ink-muted">
            {{ $hint }}
        </p>
    @endif
</div>
