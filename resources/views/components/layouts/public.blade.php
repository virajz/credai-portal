<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-zinc-950">
    <div class="flex min-h-screen flex-col">
        <!-- Header with Branding -->
        <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row sm:justify-between">
                    <!-- CREDAI Logo -->
                    <div class="flex items-center gap-4">
                        <img src="/credai_logo.png" alt="CREDAI Logo" class="h-8 w-auto sm:h-10" />
                    </div>

                    <!-- Company/Event Logo -->
                    <div class="flex items-center gap-4">
                        <img src="/logo.png" alt="Logo" class="h-12 w-auto sm:h-16" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 bg-zinc-50 dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <flux:toast />

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    @fluxScripts
</body>

</html>
