@props(['active' => false, 'href'])

@php
$classes = $active
    ? 'flex items-center min-h-[44px] gap-3 rounded-lg px-3 py-2 text-sm font-semibold bg-brand-50 text-brand-900'
    : 'flex items-center min-h-[44px] gap-3 rounded-lg px-3 py-2 text-sm font-medium text-ink-secondary hover:bg-surface-border-subtle hover:text-ink-primary transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($active) aria-current="page" @endif>
    {{ $slot }}
</a>
