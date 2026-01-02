@props([
    'title' => config('app.name') . ' - Property Exhibition',
    'backLink' => null,
    'backText' => 'Back',
    'bodyClass' => 'bg-white',
    'showHeader' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/favicon.png">

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KQXH5FV4');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="flex min-h-screen flex-col {{ $bodyClass }} antialiased" style="font-family: 'Poppins', sans-serif;">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQXH5FV4" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @if ($showHeader)
        <!-- Navigation -->
        <nav class="fixed top-0 w-full backdrop-blur-md bg-white/90 border-b border-zinc-200/50 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 lg:h-20">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" aria-label="Go to homepage">
                            <img src="/logo.png" alt="{{ config('app.name') }}"
                                class="h-10 lg:h-14 w-auto drop-shadow-sm" />
                        </a>
                    </div>

                    @if ($backLink)
                        <!-- Back Link (for detail pages) -->
                        <a href="{{ $backLink }}"
                            class="inline-flex items-center text-sm font-light text-zinc-600 hover:text-zinc-900 transition-colors">
                            <x-heroicon-o-chevron-left class="w-4 h-4 mr-2" />
                            {{ $backText }}
                        </a>
                    @else
                        <!-- Desktop Navigation (for homepage) -->
                        <flux:navbar class="-mb-px max-lg:hidden">
                            <!-- Properties Dropdown -->
                            <flux:dropdown>
                                <flux:navbar.item icon:trailing="chevron-down">Properties</flux:navbar.item>

                                <flux:navmenu>
                                    <div class="px-2 py-1.5">
                                        <flux:text size="sm">Residential</flux:text>
                                    </div>
                                    <flux:navmenu.item :href="route('properties.residential', ['subType' => '2 BHK'])">
                                        2 BHK
                                    </flux:navmenu.item>
                                    <flux:navmenu.item :href="route('properties.residential', ['subType' => '3 BHK'])">
                                        3 BHK
                                    </flux:navmenu.item>
                                    <flux:navmenu.item :href="route('properties.residential', ['subType' => '4 BHK'])">
                                        4 BHK
                                    </flux:navmenu.item>
                                    <flux:navmenu.item :href="route('properties.residential', ['subType' => '5+ BHK'])">
                                        5+ BHK
                                    </flux:navmenu.item>

                                    <flux:navmenu.separator />

                                    <div class="px-2 py-1.5">
                                        <flux:text size="sm">Commercial</flux:text>
                                    </div>
                                    <flux:navmenu.item :href="route('properties.commercial', ['subType' => 'commercial-shop'])">
                                        Showroom
                                    </flux:navmenu.item>
                                    <flux:navmenu.item :href="route('properties.commercial', ['subType' => 'commercial-office'])">
                                        Office Space
                                    </flux:navmenu.item>

                                    <flux:navmenu.separator />

                                    <flux:navmenu.item :href="route('properties.plotting')">
                                        Plotting
                                    </flux:navmenu.item>
                                    <flux:navmenu.item :href="route('properties.weekend-home')">
                                        Weekend Home
                                    </flux:navmenu.item>
                                </flux:navmenu>
                            </flux:dropdown>

                            <flux:navbar.item :href="route('public.exhibitors')">Exhibitors</flux:navbar.item>

                            <!-- CTA Button -->
                            <div class="ml-6">
                                <flux:button href="{{ route('visitor.register') }}" variant="primary">
                                    Get Free Pass
                                </flux:button>
                            </div>
                        </flux:navbar>

                        <!-- Mobile menu button -->
                        <div class="lg:hidden" x-data="{ open: false, propertiesOpen: false }">
                            <button @click="open = !open"
                                class="inline-flex items-center justify-center p-2 text-zinc-600 hover:text-zinc-900 transition-colors">
                                <x-heroicon-o-bars-3 class="h-5 w-5" />
                            </button>

                            <!-- Mobile Navigation Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                                class="absolute right-4 top-full mt-2 w-56 bg-white border border-zinc-100 rounded-lg shadow-lg">
                                <div class="py-2">
                                    <!-- Properties Expandable -->
                                    <div>
                                        <button @click="propertiesOpen = !propertiesOpen"
                                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                            <span>Properties</span>
                                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 transition-transform"
                                                x-bind:class="{ 'rotate-180': propertiesOpen }" />
                                        </button>

                                        <div x-show="propertiesOpen" x-collapse>
                                            <div class="bg-zinc-50 py-2">
                                                <!-- Residential -->
                                                <div class="px-4 py-1 text-xs font-normal text-zinc-400 uppercase">
                                                    Residential
                                                </div>
                                                <a href="{{ route('properties.residential', ['subType' => '2 BHK']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    2 BHK
                                                </a>
                                                <a href="{{ route('properties.residential', ['subType' => '3 BHK']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    3 BHK
                                                </a>
                                                <a href="{{ route('properties.residential', ['subType' => '4 BHK']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    4 BHK
                                                </a>
                                                <a href="{{ route('properties.residential', ['subType' => '5+ BHK']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    5+ BHK
                                                </a>

                                                <!-- Commercial -->
                                                <div class="px-4 py-1 mt-2 text-xs font-normal text-zinc-400 uppercase">
                                                    Commercial
                                                </div>
                                                <a href="{{ route('properties.commercial', ['subType' => 'commercial-shop']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    Showroom
                                                </a>
                                                <a href="{{ route('properties.commercial', ['subType' => 'commercial-office']) }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    Office Space
                                                </a>

                                                <!-- Plotting & Weekend Home -->
                                                <a href="{{ route('properties.plotting') }}"
                                                    class="block px-6 py-1.5 mt-2 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    Plotting
                                                </a>
                                                <a href="{{ route('properties.weekend-home') }}"
                                                    class="block px-6 py-1.5 text-sm font-light text-zinc-600 hover:text-zinc-900">
                                                    Weekend Home
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('public.exhibitors') }}"
                                        class="block px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900">Exhibitors</a>
                                    <div class="border-t border-zinc-100 my-2"></div>
                                    <a href="{{ route('visitor.register') }}"
                                        class="block px-4 py-2 text-sm font-normal text-zinc-900">Get Free
                                        Pass</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </nav>
    @endif

    <!-- Main Content -->
    <main class="flex-1 {{ $showHeader ? 'pt-16 lg:pt-20' : '' }}">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-zinc-900 border-t border-zinc-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="/logo.png" alt="{{ config('app.name') }}"
                        class="h-8 w-auto brightness-0 invert opacity-60" />
                </div>

                <!-- Venue Info -->
                <div class="text-center">
                    <p class="text-xs font-light text-zinc-400">
                        Vanita Vishram Ground, Surat &bull; January 9-11, 2026
                    </p>
                </div>

                <!-- Copyright & Links -->
                <div class="flex items-center gap-6">
                    <p class="text-xs font-light text-zinc-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </p>
                    <a href="#"
                        class="text-xs font-light text-zinc-500 hover:text-zinc-300 transition-colors">Privacy</a>
                    <a href="#"
                        class="text-xs font-light text-zinc-500 hover:text-zinc-300 transition-colors">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <flux:toast />

    <!-- Property Chatbot -->
    @livewire('property-chatbot')

    @livewireScripts
    @fluxScripts

    <!-- Google Maps Places API -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places">
    </script>

    @stack('scripts')
</body>

</html>
