<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Complete - CREDAI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <flux:card class="text-center space-y-6">
                <div class="flex justify-center">
                    <div class="rounded-full bg-green-100 dark:bg-green-900/20 p-3">
                        <flux:icon.check-circle class="size-12 text-green-600 dark:text-green-500" />
                    </div>
                </div>

                <div>
                    <flux:heading size="xl" class="mb-2">Registration Complete!</flux:heading>
                    <flux:subheading>Thank you for submitting your exhibitor information.</flux:subheading>
                </div>

                <flux:separator />

                <div class="text-left space-y-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Your exhibitor registration has been successfully submitted. Our team will review your
                        information and contact you shortly via WhatsApp.
                    </p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        If you have any questions or need to make changes, please contact the CREDAI team directly.
                    </p>
                </div>

                <flux:separator />

                <flux:button href="{{ route('home') }}" variant="ghost" icon:leading="arrow-left">
                    Return to Home
                </flux:button>
            </flux:card>
        </div>
    </div>
</body>

</html>
