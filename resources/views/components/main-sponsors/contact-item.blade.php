@props(['icon', 'label', 'value', 'truncate' => false])

<div class="flex items-center gap-3">
    <div class="w-10 h-10 bg-zinc-100 rounded-lg flex items-center justify-center flex-shrink-0">
        @php
            $iconComponent = 'heroicon-o-' . $icon;
        @endphp
        <x-dynamic-component :component="$iconComponent" class="w-5 h-5 text-zinc-600" />
    </div>
    <div class="{{ $truncate ? 'min-w-0' : '' }}">
        <p class="text-xs text-zinc-400 font-light">{{ $label }}</p>
        <p class="text-sm text-zinc-700 {{ $truncate ? 'truncate' : '' }}">{{ $value }}</p>
    </div>
</div>
