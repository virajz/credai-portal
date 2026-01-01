<div class="mx-auto max-w-3xl px-4 py-12">
    <!-- Header -->
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
            <flux:icon.user class="h-8 w-8 text-blue-600 dark:text-blue-400" variant="outline" />
        </div>
        <flux:heading size="xl" class="mb-2">Partner Profile</flux:heading>
        <flux:subheading>CREDAI Glam Property Show 2026</flux:subheading>
    </div>

    <!-- Partner Information Card -->
    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
        <flux:heading size="lg" class="mb-6">Basic Information</flux:heading>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <div class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</div>
                <div class="text-lg font-semibold">{{ $partner->first_name }} {{ $partner->last_name }}</div>
            </div>

            <div>
                <div class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Phone</div>
                <div class="text-lg font-semibold">{{ $partner->phone }}</div>
            </div>

            @if ($partner->firm_name)
                <div>
                    <div class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Firm Name</div>
                    <div class="text-lg font-semibold">{{ $partner->firm_name }}</div>
                </div>
            @endif

            @if ($partner->email)
                <div>
                    <div class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Email</div>
                    <div class="text-lg font-semibold">{{ $partner->email }}</div>
                </div>
            @endif

            <div>
                <div class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Registration Date</div>
                <div class="text-lg font-semibold">{{ $partner->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Back to Home -->
    <div class="mt-8 text-center">
        <flux:button variant="primary" href="{{ route('home') }}" icon="home" icon:variant="outline">
            Back to Home
        </flux:button>
    </div>
</div>
