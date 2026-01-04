<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Visitors</flux:heading>
            <flux:subheading>Manage visitor registrations and information</flux:subheading>
        </div>
        <div class="flex gap-3">
            <flux:button variant="outline" wire:click="openQrModal" icon="qr-code" icon:variant="outline">
                Generate QR Code
            </flux:button>
            <flux:button variant="primary" wire:click="exportVisitors" icon="arrow-down-tray" icon:variant="outline">
                Export Data
            </flux:button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by name, phone, company name, or area..." icon="magnifying-glass" />
        </div>

        @if (!empty($availableCampaigns))
            <div class="w-full lg:w-64">
                <flux:select wire:model.live="selectedCampaigns" variant="listbox" multiple searchable
                    placeholder="Filter by campaign...">
                    @foreach ($availableCampaigns as $campaign)
                        <flux:select.option value="{{ $campaign }}">{{ $campaign }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif

        <flux:button variant="outline" icon="funnel" wire:click="openFiltersModal">
            Advanced Filters
            @if ($this->hasActiveFilters())
                <flux:badge size="sm" color="blue" class="ml-2">
                    {{ $this->getActiveFilterCount() }}
                </flux:badge>
            @endif
        </flux:button>

        @if ($search || !empty($selectedCampaigns) || $this->hasActiveFilters())
            <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                Clear All
            </flux:button>
        @endif
    </div>

    <!-- Visitors Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <button wire:click="sortByColumn('name')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Name
                    @if ($sortBy === 'name')
                        @if ($sortDirection === 'asc')
                            <flux:icon.chevron-up variant="micro" />
                        @else
                            <flux:icon.chevron-down variant="micro" />
                        @endif
                    @endif
                </button>
            </flux:table.column>
            <flux:table.column>Contact</flux:table.column>
            <flux:table.column>Company</flux:table.column>
            <flux:table.column>Campaign</flux:table.column>
            <flux:table.column>Interests</flux:table.column>
            <flux:table.column>Planning to Buy</flux:table.column>
            <flux:table.column>
                <button wire:click="sortByColumn('created_at')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Registered
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
            @forelse ($visitors as $visitor)
                <flux:table.row :key="$visitor->id">
                    <flux:table.cell>
                        <div class="font-semibold">{{ $visitor->name }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm">
                            @if ($visitor->phone)
                                <div>{{ $visitor->phone }}</div>
                            @endif
                            @if ($visitor->age_group)
                                <div class="text-xs text-zinc-500">Age: {{ $visitor->age_group }}</div>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->company_name)
                            {{ $visitor->company_name }}
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->tracking_medium)
                            <flux:badge size="sm" color="blue">{{ $visitor->tracking_medium }}</flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->interests && count($visitor->interests) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach (array_slice($visitor->interests, 0, 2) as $interest)
                                    <flux:badge size="sm" color="zinc">{{ ucfirst($interest) }}</flux:badge>
                                @endforeach
                                @if (count($visitor->interests) > 2)
                                    <flux:badge size="sm" color="zinc">+{{ count($visitor->interests) - 2 }}
                                    </flux:badge>
                                @endif
                            </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->planning_to_buy)
                            <flux:badge size="sm" color="green">{{ ucfirst($visitor->planning_to_buy) }}
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <time datetime="{{ $visitor->created_at->toISOString() }}" class="text-sm">
                            {{ $visitor->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:tooltip content="View Entry/Exit Logs" position="top">
                                <flux:button variant="ghost" size="sm" icon="clock" iconVariant="outline"
                                    wire:click="showEntryExitLogs({{ $visitor->id }})" />
                            </flux:tooltip>
                            @if (auth()->user()->isAdmin())
                                <flux:tooltip content="Send WhatsApp Message" position="top">
                                    <flux:button variant="ghost" size="sm" icon="bi-whatsapp"
                                        wire:click="confirmSendWhatsApp({{ $visitor->id }})"
                                        class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400" />
                                </flux:tooltip>
                            @endif
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-48">
                                    <flux:menu.item icon="eye" icon:variant="outline"
                                        wire:click="showVisitorDetails({{ $visitor->id }})">
                                        View Details
                                    </flux:menu.item>
                                    <flux:menu.item icon="qr-code" icon:variant="outline"
                                        wire:click="showVisitorQrCode({{ $visitor->id }})">
                                        QR Code
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" icon:variant="outline" variant="danger"
                                        wire:click="confirmDelete({{ $visitor->id }})">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center">
                        <div class="py-12">
                            <flux:icon.user-group class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No visitors found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    Visitors will appear here once they register
                                @endif
                            </flux:subheading>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($visitors->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $visitors->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-visitor" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete visitor?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this visitor registration.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteVisitor">Delete visitor</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- QR Code Generation Modal -->
    <flux:modal name="generate-qr-code" class="max-w-4xl" @close="closeQrModal" wire:model="showQrModal">
        <div class="space-y-6">
            <div class="text-center">
                <flux:heading size="lg">Generate Registration QR Code</flux:heading>
                <flux:text>Enter a tracking medium to generate a unique QR code for visitor registration</flux:text>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Left Column: Input Fields -->
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Tracking Medium</flux:label>
                        <div class="flex gap-2">
                            <flux:input wire:model="qrMedium"
                                placeholder="e.g., Facebook, Instagram, Newspaper, Billboard" class="flex-1" />
                            <flux:button variant="primary" wire:click="generateQrCode" icon="qr-code"
                                icon:variant="outline">
                                Generate
                            </flux:button>
                        </div>
                        <flux:description>
                            This will be recorded with each visitor registration to track the source
                        </flux:description>
                        @error('qrMedium')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>Registration URL</flux:label>
                        <div class="flex items-stretch overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                            x-data="{
                                url: @entangle('generatedUrl'),
                                copied: false,
                                async copy() {
                                    try {
                                        const urlToCopy = this.url || '{{ route('visitor.register') }}';
                                        await navigator.clipboard.writeText(urlToCopy);
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1500);
                                    } catch (e) {
                                        console.warn('Could not copy to clipboard');
                                    }
                                }
                            }">
                            <input type="text" readonly x-bind:value="url || '{{ route('visitor.register') }}'"
                                class="w-full bg-transparent p-3 text-sm outline-none dark:text-zinc-100" />
                            <button @click="copy()"
                                class="cursor-pointer border-l border-zinc-200 px-3 transition-colors dark:border-zinc-700">
                                <flux:icon.document-duplicate x-show="!copied" variant="micro" />
                                <flux:icon.check x-show="copied" variant="micro" class="text-green-500" />
                            </button>
                        </div>
                        <flux:description>
                            Share this URL to track visitor registrations
                        </flux:description>
                    </flux:field>

                    <div x-data="{
                        hasQrCode: @entangle('qrCodeSvg'),
                        downloadPng() {
                            const svg = document.querySelector('#qr-code-svg');
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
                                        link.download = 'visitor-registration-qr-{{ Str::slug($qrMedium ?: 'code') }}.png';
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                    };
                                    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                                }
                            }
                        },
                        downloadSvg() {
                            const svg = document.querySelector('#qr-code-svg');
                            if (svg) {
                                const svgElement = svg.querySelector('svg');
                                if (svgElement) {
                                    const svgData = new XMLSerializer().serializeToString(svgElement);
                                    const blob = new Blob([svgData], { type: 'image/svg+xml' });
                                    const url = URL.createObjectURL(blob);
                                    const link = document.createElement('a');
                                    link.download = 'visitor-registration-qr-{{ Str::slug($qrMedium ?: 'code') }}.svg';
                                    link.href = url;
                                    link.click();
                                    URL.revokeObjectURL(url);
                                }
                            }
                        }
                    }">
                        <div class="grid grid-cols-2 gap-3">
                            <flux:button variant="outline" @click="downloadPng" icon="arrow-down-tray"
                                icon:variant="outline" x-bind:disabled="!hasQrCode">
                                PNG
                            </flux:button>
                            <flux:button variant="outline" @click="downloadSvg" icon="arrow-down-tray"
                                icon:variant="outline" x-bind:disabled="!hasQrCode">
                                SVG
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: QR Code Preview -->
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div
                        class="relative aspect-square w-full max-w-sm overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        @if ($qrCodeSvg)
                            <div class="flex h-full items-center justify-center bg-white p-6">
                                <div id="qr-code-svg" class="max-w-full max-h-full">
                                    {!! preg_replace('/<svg/', '<svg class="w-full h-full"', $qrCodeSvg) !!}
                                </div>
                            </div>
                        @else
                            <div
                                class="flex h-full flex-col items-center justify-center bg-zinc-50 p-6 text-center dark:bg-zinc-800">
                                <flux:icon.qr-code class="mb-3 h-16 w-16 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                    QR code will appear here
                                </flux:text>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closeQrModal">Close</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Visitor QR Code Modal -->
    <flux:modal name="visitor-qr-code" class="max-w-2xl" @close="$wire.closeVisitorQrModal()"
        wire:model="showVisitorQrModal">
        <div class="space-y-6">
            @if ($selectedVisitor)
                <div class="text-center">
                    <flux:heading size="lg">Visitor QR Code</flux:heading>
                    <flux:text class="mt-2">{{ $selectedVisitor->name }}</flux:text>
                </div>

                <div class="flex flex-col items-center justify-center space-y-4">
                    <div
                        class="relative aspect-square w-full max-w-sm overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        @if ($visitorQrCodeSvg)
                            <div class="flex h-full items-center justify-center bg-white p-6">
                                <div id="visitor-qr-code-svg" class="max-h-full max-w-full">
                                    {!! preg_replace('/<svg/', '<svg class="w-full h-full"', $visitorQrCodeSvg) !!}
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
                        visitorName: '{{ $selectedVisitor ? \Illuminate\Support\Str::slug($selectedVisitor->name) : '' }}',
                        downloadPng() {
                            const svg = document.querySelector('#visitor-qr-code-svg');
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
                                        link.download = this.visitorName + '-visitor-qr-code.png';
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                    };
                                    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                                }
                            }
                        },
                        downloadSvg() {
                            const svg = document.querySelector('#visitor-qr-code-svg');
                            if (svg) {
                                const svgElement = svg.querySelector('svg');
                                if (svgElement) {
                                    const svgData = new XMLSerializer().serializeToString(svgElement);
                                    const blob = new Blob([svgData], { type: 'image/svg+xml' });
                                    const url = URL.createObjectURL(blob);
                                    const link = document.createElement('a');
                                    link.download = this.visitorName + '-visitor-qr-code.svg';
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
                    <flux:button variant="ghost" @click="$flux.modal('visitor-qr-code').close()">Close</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Visitor Details Modal -->
    <flux:modal name="visitor-details" class="max-w-4xl" @close="$wire.closeVisitorDetailsModal()"
        wire:model="showVisitorDetailsModal">
        @if ($visitorDetails)
            <div class="space-y-6">
                <div class="text-center">
                    <flux:heading size="xl">Visitor Details</flux:heading>
                    <flux:text class="mt-2 text-lg font-medium">{{ $visitorDetails->name }}</flux:text>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Personal Details -->
                    <div
                        class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">
                            Personal Details</flux:heading>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</div>
                                <div class="font-medium">{{ $visitorDetails->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Phone</div>
                                <div class="font-medium">{{ $visitorDetails->phone }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Age Group</div>
                                <div class="font-medium">{{ $visitorDetails->age_group }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Current Residential
                                    Area</div>
                                <div class="font-medium">{{ $visitorDetails->current_residential_area }}</div>
                            </div>
                            @if ($visitorDetails->company_name)
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Company</div>
                                    <div class="font-medium">{{ $visitorDetails->company_name }}</div>
                                </div>
                            @endif
                            @if ($visitorDetails->tracking_medium)
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Campaign</div>
                                    <flux:badge color="blue">{{ $visitorDetails->tracking_medium }}</flux:badge>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Registration Date
                                </div>
                                <div class="font-medium">{{ $visitorDetails->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Interests -->
                    <div
                        class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">
                            Property Interests</flux:heading>
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Property Types
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($visitorDetails->interests as $interest)
                                        <flux:badge color="teal">{{ $interest }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>

                            @if (!empty($visitorDetails->residential_types))
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Residential
                                        Types</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($visitorDetails->residential_types as $type)
                                            <flux:badge color="blue">{{ $type }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($visitorDetails->commercial_types))
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Commercial
                                        Types</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($visitorDetails->commercial_types as $type)
                                            <flux:badge color="purple">{{ $type }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($visitorDetails->plotting_types))
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Plotting
                                        Types</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($visitorDetails->plotting_types as $type)
                                            <flux:badge color="orange">{{ $type }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Planning to Buy
                                </div>
                                <flux:badge color="green" size="lg">{{ $visitorDetails->planning_to_buy }}
                                </flux:badge>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Preferred Areas
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($visitorDetails->areas as $area)
                                        <flux:badge color="zinc">{{ $area }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:spacer />
                    <flux:button variant="ghost" @click="$flux.modal('visitor-details').close()">Close</flux:button>
                    <flux:button variant="primary"
                        wire:click="showVisitorQrCode({{ $visitorDetails->id }}); $flux.modal('visitor-details').close()"
                        icon="qr-code">
                        View QR Code
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Send WhatsApp Confirmation Modal --}}
    <flux:modal name="send-whatsapp-confirmation" class="min-w-[400px]">
        @if ($visitorToSendWhatsApp)
            @php
                $visitor = \App\Models\Visitor::find($visitorToSendWhatsApp);
            @endphp
            <div>
                <flux:heading size="lg" class="mb-1">Send WhatsApp Message</flux:heading>
                <flux:subheading class="mb-6">Are you sure you want to send a WhatsApp message to this visitor?
                </flux:subheading>

                <div
                    class="mb-6 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <flux:icon.user class="text-zinc-500" />
                            <div>
                                <div class="text-sm font-medium">{{ $visitor->name }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $visitor->phone }}</div>
                            </div>
                        </div>
                        @if ($visitor->company_name)
                            <div class="flex items-center gap-3">
                                <flux:icon.building-office class="text-zinc-500" />
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $visitor->company_name }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" @click="$flux.modal('send-whatsapp-confirmation').close()">Cancel
                    </flux:button>
                    <flux:button variant="primary" wire:click="sendWhatsApp" icon="bi-whatsapp"
                        class="bg-green-600 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700">
                        Send Message
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Entry/Exit Logs Modal -->
    <flux:modal name="entry-exit-logs" class="md:w-7xl">
        @if ($showEntryExitModal && $visitorForEntries)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Entry/Exit Logs</flux:heading>
                    <flux:subheading>{{ $visitorForEntries->name }} - {{ $visitorForEntries->phone }}
                    </flux:subheading>
                </div>

                @if ($visitorForEntries->entryExitLogs && $visitorForEntries->entryExitLogs->count() > 0)
                    <div class="space-y-4">
                        @foreach ($visitorForEntries->entryExitLogs as $log)
                            <flux:card>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Entry Time
                                        </div>
                                        <div class="text-lg">
                                            @if ($log->entry_time)
                                                {{ $log->entry_time->format('M d, Y') }}<br>
                                                <span
                                                    class="text-sm text-zinc-500">{{ $log->entry_time->format('h:i A') }}</span>
                                            @else
                                                <span class="text-zinc-400">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Exit Time
                                        </div>
                                        <div class="text-lg">
                                            @if ($log->exit_time)
                                                {{ $log->exit_time->format('M d, Y') }}<br>
                                                <span
                                                    class="text-sm text-zinc-500">{{ $log->exit_time->format('h:i A') }}</span>
                                            @else
                                                <flux:badge color="yellow">Still Inside</flux:badge>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($log->entry_time && $log->exit_time)
                                    <flux:separator class="my-3" />
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                        <span class="font-semibold">Duration:</span>
                                        {{ $log->entry_time->diff($log->exit_time)->format('%H hours %I minutes') }}
                                    </div>
                                @endif
                            </flux:card>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <flux:icon.arrow-right-start-on-rectangle class="h-12 w-12 text-zinc-400 mb-4" />
                        <flux:heading size="lg" class="text-zinc-600 dark:text-zinc-400">No Entry/Exit Logs
                        </flux:heading>
                        <flux:subheading>This visitor hasn't entered or exited yet.</flux:subheading>
                    </div>
                @endif

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="primary" wire:click="closeEntryExitModal">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <x-visitors-filter-modal :filterOptions="$filterOptions" />
</div>
