<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>Registration Link Locked - {{ config('app.name') }}</title>
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
        <main class="flex flex-1 items-center justify-center bg-zinc-50 px-4 dark:bg-zinc-950">
            <div class="w-full max-w-2xl text-center">
                <!-- Lock Icon -->
                <div class="mb-8 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/50">
                        <svg class="h-12 w-12 text-red-600 dark:text-red-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="mb-4 text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                    Registration Link Expired
                </h1>

                <!-- Message -->
                <div class="space-y-4">
                    <div
                        class="mx-auto max-w-md rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="mb-3 text-lg font-semibold text-zinc-900 dark:text-white">
                            Need Assistance?
                        </h2>
                        <p class="mb-4 text-sm text-zinc-600 dark:text-zinc-400">
                            If you believe this is an error or need to access this registration form, please contact the
                            CREDAI administrators for assistance.
                        </p>
                    </div>
                </div>
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

    @fluxScripts
</body>

</html>
