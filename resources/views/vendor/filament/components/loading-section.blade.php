@props([
    'columnSpan' => [],
    'columnStart' => [],
    'height' => null,
    'loadingLabel' => null,
])

@php
    use Filament\Support\View\ComponentAttributeBag;

    // Filter out array properties (like dashboard widget filters) that Livewire lazy
    // loading incorrectly injects into the placeholder view attributes.
    if (isset($attributes)) {
        $attributes = $attributes->filter(fn ($value) => ! is_array($value));
    }
@endphp

<div
    role="status"
    aria-busy="true"
    {{
        ($attributes ?? new ComponentAttributeBag)
            ->gridColumn($columnSpan, $columnStart)
            ->class(['fi-section fi-loading-section'])
            ->style(['height: ' . e($height ?? '8rem')])
    }}
>
    <span class="fi-sr-only">
        {{ $loadingLabel ?? __('filament::components/loading-section.label') }}
    </span>
</div>
