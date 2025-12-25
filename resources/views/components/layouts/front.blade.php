@props([
    'title' => config('app.name') . ' - Property Exhibition',
    'backLink' => null,
    'backText' => 'Back',
    'bodyClass' => 'bg-white',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="flex min-h-screen flex-col {{ $bodyClass }} antialiased" style="font-family: 'Poppins', sans-serif;">
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
                    <div class="hidden lg:flex items-center space-x-1">
                        <a href="{{ route('public.exhibitors') }}"
                            class="px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900 transition-colors">
                            Exhibitors
                        </a>
                        <a href="{{ route('home') }}#features"
                            class="px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900 transition-colors">
                            Why Visit
                        </a>

                        <!-- CTA Button -->
                        <div class="ml-6">
                            <flux:button href="{{ route('visitor.register') }}" variant="primary">
                                Get Free Pass
                            </flux:button>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center justify-center p-2 text-zinc-600 hover:text-zinc-900 transition-colors">
                            <x-heroicon-o-bars-3 class="h-5 w-5" />
                        </button>

                        <!-- Mobile Navigation Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            @click.away="open = false"
                            class="absolute right-4 top-full mt-2 w-48 bg-white border border-zinc-100 rounded-lg shadow-lg">
                            <div class="py-2">
                                <a href="{{ route('public.exhibitors') }}"
                                    class="block px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900">Exhibitors</a>
                                <a href="{{ route('home') }}#features"
                                    class="block px-4 py-2 text-sm font-light text-zinc-600 hover:text-zinc-900">Why
                                    Visit</a>
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

    <!-- Main Content -->
    <main class="flex-1 pt-16 lg:pt-20">
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
