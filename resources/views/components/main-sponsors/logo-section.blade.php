@props(['sponsor', 'position'])

@php
    $gradientClass = $position === 'left' ? 'from-zinc-50 to-white' : 'from-white to-zinc-50';
    $borderClass =
        $position === 'left'
            ? 'border-b md:border-b-0 md:border-r border-zinc-200'
            : 'border-b md:border-b-0 md:border-l border-zinc-200';
@endphp

<div class="md:w-2/5 bg-gradient-to-br {{ $gradientClass }} p-12 flex items-center justify-center {{ $borderClass }}">
    @if ($sponsor->preview_logo ?? $sponsor->logo_path)
        <img src="{{ Storage::url($sponsor->preview_logo ?? $sponsor->logo_path) }}" alt="{{ $sponsor->brand_name }}"
            class="w-full max-w-xs h-auto object-contain" />
    @else
        <x-heroicon-o-building-office class="w-32 h-32 text-zinc-300" />
    @endif
</div>
