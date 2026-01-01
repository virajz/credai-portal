<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">Welcome, {{ auth('exhibitor')->user()->company_name }}!</flux:heading>
        <flux:subheading>Here's your analytics overview</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Profile Views</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['profile_views']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Project Views</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['project_views']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Brochure Downloads</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['brochure_downloads']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Website Visits</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['website_visits']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Total Calls</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['calls']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Total Leads</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['total_leads']) }}</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col gap-2">
                <flux:subheading>Total Events</flux:subheading>
                <div class="text-3xl font-bold">{{ number_format($summary['total_events']) }}</div>
            </div>
        </flux:card>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <flux:card>
            <flux:heading size="lg">Quick Actions</flux:heading>
            <div class="mt-4 flex flex-col gap-3">
                <flux:button variant="primary" :href="route('exhibitor.scan')" wire:navigate icon="qr-code">
                    Scan Visitor QR Code
                </flux:button>
                <flux:button variant="ghost" :href="route('exhibitor.leads')" wire:navigate icon="user-group">
                    View My Leads
                </flux:button>
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Company Information</flux:heading>
            <div class="mt-4 space-y-2 text-sm">
                <div>
                    <span class="font-semibold">Company:</span>
                    <span>{{ auth('exhibitor')->user()->company_name }}</span>
                </div>
                <div>
                    <span class="font-semibold">Contact:</span>
                    <span>{{ auth('exhibitor')->user()->registered_number }}</span>
                </div>
                @if(auth('exhibitor')->user()->stall_number)
                    <div>
                        <span class="font-semibold">Stall:</span>
                        <span>{{ auth('exhibitor')->user()->stall_number }}</span>
                    </div>
                @endif
            </div>
        </flux:card>
    </div>
</div>
