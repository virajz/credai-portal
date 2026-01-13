<div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-12 text-center">
        <flux:heading size="2xl" class="mb-6">
            <span
                class="bg-linear-to-r from-teal-700 via-teal-600 to-teal-600 bg-clip-text text-transparent dark:from-teal-400 dark:via-teal-300 dark:to-teal-300">
                Visitor Registration
            </span>
        </flux:heading>
    </div>

    <flux:card class="backdrop-blur-sm">
        <div class="space-y-6 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                <flux:icon.information-circle variant="solid" class="size-8 text-teal-600 dark:text-teal-400" />
            </div>

            <div class="space-y-2">
                <flux:heading size="xl">Registration Closed</flux:heading>
                <flux:subheading class="text-zinc-600 dark:text-zinc-400">
                    Visitor registration for CREDAI Glam Property Show 2026 has ended.
                </flux:subheading>
            </div>

            <flux:separator />

            <div class="space-y-4">
                <p class="text-zinc-700 dark:text-zinc-300">
                    Thank you for your interest! The exhibition is now over.
                </p>

                <flux:button variant="primary" href="{{ route('public.exhibitors') }}" wire:navigate>
                    Explore Exhibitors
                </flux:button>
            </div>

            <flux:separator />

            <div class="pt-2">
                <flux:button variant="ghost" href="{{ route('home') }}" wire:navigate>
                    Back to Home
                </flux:button>
            </div>
        </div>
    </flux:card>
</div>
