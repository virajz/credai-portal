<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Partners</flux:heading>
            <flux:subheading>Manage partner registrations and information</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="exportData" icon="arrow-down-tray" icon:variant="outline">
            Export Data
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by name, phone, firm name, or email..." icon="magnifying-glass" />
        </div>

        @if ($search)
            <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                Clear Filters
            </flux:button>
        @endif
    </div>

    <!-- Partners Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <button wire:click="sortByColumn('first_name')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Name
                    @if ($sortBy === 'first_name')
                        @if ($sortDirection === 'asc')
                            <flux:icon.chevron-up variant="micro" />
                        @else
                            <flux:icon.chevron-down variant="micro" />
                        @endif
                    @endif
                </button>
            </flux:table.column>
            <flux:table.column>Contact</flux:table.column>
            <flux:table.column>Firm</flux:table.column>
            <flux:table.column>Property Types</flux:table.column>
            <flux:table.column>Areas</flux:table.column>
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
            @forelse ($partners as $partner)
                <flux:table.row :key="$partner->id">
                    <flux:table.cell>
                        <div class="font-semibold">{{ $partner->first_name }} {{ $partner->last_name }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm">
                            @if ($partner->phone)
                                <div>{{ $partner->phone }}</div>
                            @endif
                            @if ($partner->email)
                                <div class="text-xs text-zinc-500">{{ $partner->email }}</div>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($partner->firm_name)
                            {{ $partner->firm_name }}
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($partner->property_types && count($partner->property_types) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach (array_slice($partner->property_types, 0, 2) as $type)
                                    <flux:badge size="sm" color="purple">{{ ucfirst($type) }}</flux:badge>
                                @endforeach
                                @if (count($partner->property_types) > 2)
                                    <flux:badge size="sm" color="purple">+{{ count($partner->property_types) - 2 }}
                                    </flux:badge>
                                @endif
                            </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($partner->areas && count($partner->areas) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach (array_slice($partner->areas, 0, 2) as $area)
                                    <flux:badge size="sm" color="zinc">{{ ucfirst($area) }}</flux:badge>
                                @endforeach
                                @if (count($partner->areas) > 2)
                                    <flux:badge size="sm" color="zinc">+{{ count($partner->areas) - 2 }}
                                    </flux:badge>
                                @endif
                            </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <time datetime="{{ $partner->created_at->toISOString() }}" class="text-sm">
                            {{ $partner->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            @if(auth()->user()->isAdmin())
                                <flux:tooltip content="Send WhatsApp Message" position="top">
                                    <flux:button variant="ghost" size="sm" icon="bi-whatsapp"
                                        wire:click="confirmSendWhatsApp({{ $partner->id }})"
                                        class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400" />
                                </flux:tooltip>
                            @endif
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-48">
                                    <flux:menu.item icon="eye" icon:variant="outline"
                                        wire:click="showPartnerDetails({{ $partner->id }})">
                                        View Details
                                    </flux:menu.item>
                                    <flux:menu.item icon="qr-code" icon:variant="outline"
                                        wire:click="showPartnerQrCode({{ $partner->id }})">
                                        QR Code
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" icon:variant="outline" variant="danger"
                                        wire:click="confirmDelete({{ $partner->id }})">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        <div class="py-12">
                            <flux:icon.user-group class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No partners found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    Partners will appear here once they register
                                @endif
                            </flux:subheading>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($partners->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $partners->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-partner" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete partner?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this partner registration.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deletePartner">Delete partner</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Partner QR Code Modal -->
    <flux:modal name="partner-qr-code" class="max-w-2xl" @close="$wire.closePartnerQrModal()" wire:model="showPartnerQrModal">
        <div class="space-y-6">
            @if ($selectedPartner)
                <div class="text-center">
                    <flux:heading size="lg">Partner QR Code</flux:heading>
                    <flux:text class="mt-2">{{ $selectedPartner->first_name }} {{ $selectedPartner->last_name }}</flux:text>
                </div>

                <div class="flex flex-col items-center justify-center space-y-4">
                    <div
                        class="relative aspect-square w-full max-w-sm overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        @if ($partnerQrCodeSvg)
                            <div class="flex h-full items-center justify-center bg-white p-6">
                                <div id="partner-qr-code-svg" class="max-h-full max-w-full">
                                    {!! preg_replace('/<svg/', '<svg class="w-full h-full"', $partnerQrCodeSvg) !!}
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
                        partnerName: '{{ $selectedPartner ? \Illuminate\Support\Str::slug($selectedPartner->first_name . '-' . $selectedPartner->last_name) : '' }}',
                        downloadPng() {
                            const svg = document.querySelector('#partner-qr-code-svg');
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
                                        link.download = this.partnerName + '-partner-qr-code.png';
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                    };
                                    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                                }
                            }
                        },
                        downloadSvg() {
                            const svg = document.querySelector('#partner-qr-code-svg');
                            if (svg) {
                                const svgElement = svg.querySelector('svg');
                                if (svgElement) {
                                    const svgData = new XMLSerializer().serializeToString(svgElement);
                                    const blob = new Blob([svgData], { type: 'image/svg+xml' });
                                    const url = URL.createObjectURL(blob);
                                    const link = document.createElement('a');
                                    link.download = this.partnerName + '-partner-qr-code.svg';
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
                    <flux:button variant="ghost" @click="$flux.modal('partner-qr-code').close()">Close</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Partner Details Modal -->
    <flux:modal name="partner-details" class="max-w-4xl" @close="$wire.closePartnerDetailsModal()" wire:model="showPartnerDetailsModal">
        @if ($partnerDetails)
            <div class="space-y-6">
                <div class="text-center">
                    <flux:heading size="xl">Partner Details</flux:heading>
                    <flux:text class="mt-2 text-lg font-medium">{{ $partnerDetails->first_name }} {{ $partnerDetails->last_name }}</flux:text>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Personal Details -->
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">Personal Details</flux:heading>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</div>
                                <div class="font-medium">{{ $partnerDetails->first_name }} {{ $partnerDetails->last_name }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Phone</div>
                                <div class="font-medium">{{ $partnerDetails->phone }}</div>
                            </div>
                            @if ($partnerDetails->email)
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Email</div>
                                    <div class="font-medium">{{ $partnerDetails->email }}</div>
                                </div>
                            @endif
                            @if ($partnerDetails->firm_name)
                                <div>
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Firm Name</div>
                                    <div class="font-medium">{{ $partnerDetails->firm_name }}</div>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Registration Date</div>
                                <div class="font-medium">{{ $partnerDetails->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Interests -->
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">Property Interests</flux:heading>
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Property Types</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($partnerDetails->property_types as $type)
                                        <flux:badge color="purple">{{ $type }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Preferred Areas</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($partnerDetails->areas as $area)
                                        <flux:badge color="zinc">{{ $area }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:spacer />
                    <flux:button variant="ghost" @click="$flux.modal('partner-details').close()">Close</flux:button>
                    <flux:button variant="primary" wire:click="showPartnerQrCode({{ $partnerDetails->id }}); $flux.modal('partner-details').close()" icon="qr-code">
                        View QR Code
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Send WhatsApp Confirmation Modal --}}
    <flux:modal name="send-whatsapp" class="min-w-[400px]">
        @if ($partnerToSendWhatsApp)
            @php
                $partner = \App\Models\Partner::find($partnerToSendWhatsApp);
            @endphp
            <div>
                <flux:heading size="lg" class="mb-1">Send WhatsApp Message</flux:heading>
                <flux:subheading class="mb-6">Are you sure you want to send a WhatsApp message to this partner?</flux:subheading>

                <div class="mb-6 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <flux:icon.user class="text-zinc-500" />
                            <div>
                                <div class="text-sm font-medium">{{ $partner->first_name }} {{ $partner->last_name }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $partner->phone }}</div>
                            </div>
                        </div>
                        @if ($partner->firm_name)
                            <div class="flex items-center gap-3">
                                <flux:icon.building-office class="text-zinc-500" />
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $partner->firm_name }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="cancelSendWhatsApp">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="sendWhatsApp" icon="bi-whatsapp"
                        class="bg-green-600 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700">
                        Send Message
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
