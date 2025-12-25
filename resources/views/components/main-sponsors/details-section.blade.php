@props(['sponsor'])

<div class="md:w-3/5 p-8 md:p-12 flex flex-col justify-center">
    <!-- Header -->
    <div class="mb-6">
        @if ($sponsor->company?->stall_number)
            <span
                class="inline-flex items-center px-3 py-1 text-xs font-medium bg-teal-100 text-teal-700 rounded-full mb-3">
                Stall {{ $sponsor->company->stall_number }}
            </span>
        @endif
        <h3 class="text-2xl md:text-3xl font-light text-zinc-900 group-hover:text-zinc-700 transition-colors mb-4">
            {{ $sponsor->brand_name }}
        </h3>
        @if ($sponsor->company?->main_person_name)
            <p class="text-sm text-zinc-600 font-light mb-2">
                Contact: {{ $sponsor->company->main_person_name }}
            </p>
        @endif
    </div>

    <!-- Contact Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        @if ($sponsor->city)
            <x-main-sponsors.contact-item icon="map-pin" label="Location" :value="$sponsor->city" />
        @endif

        @if ($sponsor->phone_number)
            <x-main-sponsors.contact-item icon="phone" label="Phone" :value="$sponsor->phone_number" />
        @endif

        @if ($sponsor->website)
            <x-main-sponsors.contact-item icon="globe-alt" label="Website" :value="parse_url($sponsor->website, PHP_URL_HOST) ?? $sponsor->website" truncate />
        @endif

        @if ($sponsor->projects->count() > 0)
            <x-main-sponsors.contact-item icon="building-office-2" label="Projects" :value="$sponsor->projects->count() . ' ' . Str::plural('Project', $sponsor->projects->count())" />
        @endif
    </div>

    <!-- CTA -->
    <div class="flex items-center gap-2 text-teal-600 group-hover:text-teal-700 transition-colors">
        <span class="text-sm font-medium">Explore Projects</span>
        <x-heroicon-o-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
    </div>
</div>
