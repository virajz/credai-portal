<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Companies</flux:heading>
            <flux:subheading>Manage companies and their stall allocations</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button variant="ghost" icon="qr-code" wire:click="downloadAllQrCodes">
                Download All QR Codes
            </flux:button>
            <flux:button variant="ghost" icon="arrow-down-tray" wire:click="exportCompanies">
                Export CSV
            </flux:button>
            <flux:button variant="primary" :href="route('companies.create')" icon="plus" wire:navigate>
                Add Company
            </flux:button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by company name, main person, registered number, or stall number..."
                icon="magnifying-glass" />
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" icon="funnel" icon:variant="outline" wire:click="toggleFilterDrawer">
                Filters
                @if ($this->hasActiveFilters())
                    <flux:badge size="sm" color="blue">
                        {{ collect([$filterStatus, $filterLockStatus, $filterStallAssignment, $filterCategory, $filterLogoStatus])->filter()->count() }}
                    </flux:badge>
                @endif
            </flux:button>

            @if ($this->hasActiveFilters())
                <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                    Clear All
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Companies Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <button wire:click="sortByColumn('company_name')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Company Name
                    @if ($sortBy === 'company_name')
                        @if ($sortDirection === 'asc')
                            <flux:icon.chevron-up variant="micro" />
                        @else
                            <flux:icon.chevron-down variant="micro" />
                        @endif
                    @endif
                </button>
            </flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Main Person</flux:table.column>
            <flux:table.column>Registered Number</flux:table.column>
            <flux:table.column>Stall Details</flux:table.column>
            <flux:table.column>Projects</flux:table.column>
            <flux:table.column>
                <button wire:click="sortByColumn('created_at')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Created
                    @if ($sortBy === 'created_at')
                        @if ($sortDirection === 'asc')
                            <flux:icon.chevron-up variant="micro" />
                        @else
                            <flux:icon.chevron-down variant="micro" />
                        @endif
                    @endif
                </button>
            </flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($companies as $company)
                <flux:table.row :key="$company->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <div class="font-semibold">
                                <a href="{{ route('companies.edit', $company->id) }}"
                                    wire:navigate>{{ $company->company_name }}</a>
                            </div>
                            @if ($company->has_submitted)
                                <flux:badge size="sm" color="green" icon="check-circle">Submitted</flux:badge>
                            @endif
                            @if ($company->is_locked)
                                <flux:badge size="sm" color="red" icon="lock-closed">Locked</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$company->category === 'Builders' ? 'blue' : 'purple'">
                            {{ $company->category }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div>{{ $company->main_person_name }}</div>
                        <div class="text-xs text-zinc-500">{{ $company->registered_number }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $company->registered_number }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($company->stall_number)
                            <div class="text-sm">
                                <div class="font-medium">{{ $company->stall_number }}</div>
                                @if ($company->stall_type || $company->stall_size)
                                    <div class="text-xs text-zinc-500">
                                        {{ $company->stall_type }}
                                        @if ($company->stall_type && $company->stall_size)
                                            •
                                        @endif
                                        {{ $company->stall_size }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-zinc-400">Not assigned</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $company->exhibitor?->projects?->count() ?? 0 }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <time datetime="{{ $company->created_at->toISOString() }}" class="text-sm">
                            {{ $company->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2" x-data="{ copied: false }"
                            @copy-to-clipboard.window="if ($event.detail.url) { navigator.clipboard.writeText($event.detail.url).then(() => { copied = true; setTimeout(() => copied = false, 2000) }) }">
                            @if ($company->has_submitted)
                                <flux:tooltip content="View Submission" position="top">
                                    <flux:button :href="route('companies.submission', $company)" variant="ghost"
                                        size="sm" icon="document-magnifying-glass" icon:variant="outline"
                                        wire:navigate />
                                </flux:tooltip>
                            @endif

                            <div x-tooltip="copied ? 'Copied!' : 'Copy registration link'">
                                <flux:button wire:click="generateAndCopyLink({{ $company->id }})" variant="ghost"
                                    size="sm" icon="clipboard" icon:variant="outline" />
                            </div>

                            {{-- QR and Lock actions moved into the dropdown menu below --}}

                            <flux:tooltip content="More actions" position="top">
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu class="w-48">
                                        <flux:menu.item icon="qr-code" icon:variant="outline"
                                            wire:click="showCompanyQrCode({{ $company->id }})">Generate QR Code
                                        </flux:menu.item>

                                        @if ($company->is_locked)
                                            <flux:menu.item icon="lock-open" icon:variant="outline"
                                                wire:click="toggleLock({{ $company->id }})">Unlock registration link
                                            </flux:menu.item>
                                        @else
                                            <flux:menu.item icon="lock-closed" icon:variant="outline"
                                                wire:click="toggleLock({{ $company->id }})">Lock registration link
                                            </flux:menu.item>
                                        @endif

                                        <flux:menu.separator />
                                        <flux:menu.item icon="pencil" icon:variant="outline"
                                            :href="route('companies.edit', $company)" wire:navigate>Edit
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" icon:variant="outline" variant="danger"
                                            wire:click="confirmDelete({{ $company->id }})">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:tooltip>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center">
                        <div class="py-12">
                            <flux:icon.inbox class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No companies found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    Get started by adding your first company
                                @endif
                            </flux:subheading>
                            @if (!$search)
                                <flux:button variant="primary" :href="route('companies.create')" icon="plus"
                                    wire:navigate>
                                    Add Company
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($companies->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $companies->links() }}
        </div>
    @endif

    <!-- Filter Drawer -->
    <flux:modal name="filter-drawer" variant="flyout" wire:model="filterDrawerOpen">
        <form class="space-y-6" wire:submit="applyFilters">
            <div>
                <flux:heading size="lg">Filter Companies</flux:heading>
                <flux:subheading>Refine your company list with advanced filters</flux:subheading>
            </div>

            <flux:separator />

            <!-- Category Filter -->
            <flux:field>
                <flux:label>Category</flux:label>
                <flux:radio.group wire:model.live="filterCategory">
                    <flux:radio value="" label="All Categories" />
                    <flux:radio value="builders" label="Builders" />
                    <flux:radio value="allied" label="Allied" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Submission Status Filter -->
            <flux:field>
                <flux:label>Submission Status</flux:label>
                <flux:radio.group wire:model.live="filterStatus">
                    <flux:radio value="" label="All Companies" />
                    <flux:radio value="submitted" label="Submitted" />
                    <flux:radio value="pending" label="Pending" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Lock Status Filter -->
            <flux:field>
                <flux:label>Lock Status</flux:label>
                <flux:radio.group wire:model.live="filterLockStatus">
                    <flux:radio value="" label="All" />
                    <flux:radio value="locked" label="Locked" />
                    <flux:radio value="unlocked" label="Unlocked" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Stall Assignment Filter -->
            <flux:field>
                <flux:label>Stall Assignment</flux:label>
                <flux:radio.group wire:model.live="filterStallAssignment">
                    <flux:radio value="" label="All" />
                    <flux:radio value="assigned" label="Assigned" />
                    <flux:radio value="unassigned" label="Not Assigned" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Logo Status Filter -->
            <flux:field>
                <flux:label>Logo Status</flux:label>
                <flux:radio.group wire:model.live="filterLogoStatus">
                    <flux:radio value="" label="All Submissions" />
                    <flux:radio value="with_logo" label="Has Logo" />
                    <flux:radio value="without_logo" label="Missing Logo" />
                    <flux:radio value="no_submission" label="No Submission" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="clearFilters" type="button">Clear All</flux:button>
                <flux:button variant="primary" type="submit">Apply Filters</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-company" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete company?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this company.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteCompany">Delete company</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Company QR Code Modal -->
    <flux:modal name="company-qr-code" class="max-w-2xl" @close="$wire.closeCompanyQrModal()"
        wire:model="showCompanyQrModal">
        <div class="space-y-6">
            @if ($selectedCompany)
                <div class="text-center">
                    <flux:heading size="lg">Company QR Code</flux:heading>
                    <flux:text class="mt-2">{{ $selectedCompany->company_name }}</flux:text>
                    <flux:text class="mt-1 text-sm text-zinc-500">Scans will open WhatsApp to inquire about this
                        company</flux:text>
                </div>

                <div class="flex flex-col items-center justify-center space-y-4">
                    <div
                        class="relative aspect-square w-full max-w-sm overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        @if ($companyQrCodeSvg)
                            <div class="flex h-full items-center justify-center bg-white p-6">
                                <div id="company-qr-code-svg" class="max-h-full max-w-full">
                                    {!! preg_replace('/<svg/', '<svg class="w-full h-full"', $companyQrCodeSvg) !!}
                                </div>
                            </div>
                        @else
                            <div
                                class="flex h-full flex-col items-center justify-center bg-zinc-50 p-6 text-center dark:bg-zinc-800">
                                <flux:icon.qr-code class="mb-3 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Loading QR code...
                                </flux:text>
                            </div>
                        @endif
                    </div>

                    <div class="grid w-full max-w-sm grid-cols-2 gap-3" x-data="{
                        companyName: '{{ $selectedCompany ? \Illuminate\Support\Str::slug($selectedCompany->company_name) : '' }}',
                        downloadPng() {
                            const svg = document.querySelector('#company-qr-code-svg');
                            if (svg) {
                                const svgElement = svg.querySelector('svg');
                                if (svgElement) {
                                    const svgData = new XMLSerializer().serializeToString(svgElement);
                                    const canvas = document.createElement('canvas');
                                    const ctx = canvas.getContext('2d');
                                    const img = new Image();
                                    img.onload = () => {
                                        canvas.width = 1200;
                                        canvas.height = 1200;
                                        ctx.fillStyle = 'white';
                                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                                        ctx.drawImage(img, 0, 0, 1200, 1200);
                                        const link = document.createElement('a');
                                        link.download = this.companyName + '-exhibitor-qr-code.png';
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                    };
                                    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                                }
                            }
                        },
                        downloadSvg() {
                            const svg = document.querySelector('#company-qr-code-svg');
                            if (svg) {
                                const svgElement = svg.querySelector('svg');
                                if (svgElement) {
                                    const svgData = new XMLSerializer().serializeToString(svgElement);
                                    const blob = new Blob([svgData], { type: 'image/svg+xml' });
                                    const url = URL.createObjectURL(blob);
                                    const link = document.createElement('a');
                                    link.download = this.companyName + '-exhibitor-qr-code.svg';
                                    link.href = url;
                                    link.click();
                                    URL.revokeObjectURL(url);
                                }
                            }
                        }
                    }">
                        <flux:button variant="outline" @click="downloadPng" icon="arrow-down-tray"
                            icon:variant="outline">
                            Download PNG
                        </flux:button>
                        <flux:button variant="outline" @click="downloadSvg" icon="arrow-down-tray"
                            icon:variant="outline">
                            Download SVG
                        </flux:button>
                    </div>
                </div>

                <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:spacer />
                    <flux:button variant="ghost" @click="$flux.modal('company-qr-code').close()">Close</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
