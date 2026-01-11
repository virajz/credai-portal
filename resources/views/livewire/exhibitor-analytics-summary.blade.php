<div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
    <!-- Profile Views -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($this->summary['profile_views']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Profile Views</div>
        </div>
    </flux:card>

    <!-- Project Views -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ number_format($this->summary['project_views']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Project Views</div>
        </div>
    </flux:card>

    <!-- Brochure Downloads -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-teal-600 dark:text-teal-400">
                {{ number_format($this->summary['brochure_downloads'] + $this->summary['project_brochure_downloads']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Downloads</div>
        </div>
    </flux:card>

    <!-- Calls -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-amber-600 dark:text-amber-400">
                {{ number_format($this->summary['calls']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Calls</div>
        </div>
    </flux:card>

    <!-- Website Visits -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-rose-600 dark:text-rose-400">
                {{ number_format($this->summary['website_visits']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Website Visits</div>
        </div>
    </flux:card>

    <!-- QR Scans -->
    <flux:card>
        <div class="text-center">
            <div class="mb-2 text-3xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($this->summary['qr_scans']) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">QR Scans</div>
        </div>
    </flux:card>
</div>
