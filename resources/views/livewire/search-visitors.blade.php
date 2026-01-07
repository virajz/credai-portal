<div class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-8">
        <flux:heading size="xl" class="mb-2">Visitor Search</flux:heading>
        <flux:subheading>Search for visitors and view recent registrations</flux:subheading>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Recent Registrations Section (Auto-refresh every 6 seconds) -->
        <div wire:poll.6s class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="mb-4 flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-700">
                <flux:heading size="lg">Recent Registrations</flux:heading>
                <flux:badge color="zinc" size="sm">Last 20</flux:badge>
            </div>

            <div class="space-y-2 max-h-150 overflow-y-auto">
                @forelse($recentVisitors as $visitor)
                    <div class="flex items-center gap-3 rounded-lg border border-zinc-100 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $visitor->name }}
                            </div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $visitor->phone }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $visitor->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <flux:tooltip content="Send WhatsApp" position="top">
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="bi-whatsapp"
                                    wire:click="confirmSendWhatsApp({{ $visitor->id }})"
                                    class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
                                />
                            </flux:tooltip>
                            <div x-data="{
                                async downloadImage() {
                                    try {
                                        const response = await fetch('{{ route('visitor.success', $visitor) }}');
                                        const html = await response.text();
                                        const parser = new DOMParser();
                                        const doc = parser.parseFromString(html, 'text/html');
                                        const imgElement = doc.querySelector('#whatsapp-qr-image');

                                        if (imgElement && imgElement.src) {
                                            const link = document.createElement('a');
                                            link.download = '{{ \Illuminate\Support\Str::slug($visitor->name) }}-visitor-qr-code.png';
                                            link.href = imgElement.src;
                                            link.click();
                                        } else {
                                            alert('Image not found');
                                        }
                                    } catch (error) {
                                        console.error('Download failed:', error);
                                        alert('Failed to download image');
                                    }
                                }
                            }">
                                <flux:tooltip content="Download QR Image" position="top">
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="arrow-down-tray"
                                        @click="downloadImage"
                                    />
                                </flux:tooltip>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <flux:icon.users class="mx-auto mb-2 h-12 w-12 text-zinc-400" variant="outline" />
                        <flux:text class="text-zinc-500">No recent registrations</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Search Section -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-3 dark:border-zinc-700">
                Search by Phone Number
            </flux:heading>

            <div class="mb-6">
                <flux:field>
                    <flux:label>Phone Number (10 digits)</flux:label>
                    <flux:input
                        wire:model.live="searchPhone"
                        type="text"
                        placeholder="Enter 10-digit phone number"
                        maxlength="10"
                        inputmode="numeric"
                        pattern="[0-9]*"
                    />
                    <flux:description>Start typing to auto-search after 10 digits</flux:description>
                </flux:field>

                @if($searchPhone && strlen($searchPhone) === 10)
                    <div class="mt-3">
                        <flux:button
                            wire:click="clearSearch"
                            variant="ghost"
                            size="sm"
                            icon="x-mark"
                            icon:variant="outline"
                        >
                            Clear Search
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Search Results -->
            @if($foundVisitor)
                <div class="space-y-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <flux:icon.user class="h-5 w-5 text-green-600 dark:text-green-400" variant="outline" />
                        </div>
                        <div class="flex-1">
                            <flux:heading size="lg" class="text-green-900 dark:text-green-100">
                                {{ $foundVisitor->name }}
                            </flux:heading>
                            <flux:text class="text-green-700 dark:text-green-300">
                                {{ $foundVisitor->phone }}
                            </flux:text>
                            @if($foundVisitor->company_name)
                                <flux:text class="text-sm text-green-600 dark:text-green-400">
                                    {{ $foundVisitor->company_name }}
                                </flux:text>
                            @endif
                            <flux:text class="text-xs text-green-600 dark:text-green-400">
                                Registered: {{ $foundVisitor->created_at->format('M d, Y h:i A') }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 pt-3 border-t border-green-200 dark:border-green-800">
                        <flux:button
                            wire:click="confirmSendWhatsApp({{ $foundVisitor->id }})"
                            variant="primary"
                            icon="bi-whatsapp"
                            class="w-full text-white"
                        >
                            Send WhatsApp
                        </flux:button>

                        <div x-data="{
                            async downloadImage() {
                                try {
                                    const response = await fetch('{{ route('visitor.success', $foundVisitor) }}');
                                    const html = await response.text();
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const imgElement = doc.querySelector('#whatsapp-qr-image');

                                    if (imgElement && imgElement.src) {
                                        const link = document.createElement('a');
                                        link.download = '{{ \Illuminate\Support\Str::slug($foundVisitor->name) }}-visitor-qr-code.png';
                                        link.href = imgElement.src;
                                        link.click();
                                    } else {
                                        alert('Image not found');
                                    }
                                } catch (error) {
                                    console.error('Download failed:', error);
                                    alert('Failed to download image');
                                }
                            }
                        }">
                            <flux:button
                                @click="downloadImage"
                                variant="outline"
                                icon="arrow-down-tray"
                                icon:variant="outline"
                                class="w-full"
                            >
                                Download QR Image
                            </flux:button>
                        </div>
                    </div>
                </div>
            @elseif($searchPhone && strlen($searchPhone) === 10)
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950">
                    <div class="flex items-center gap-3">
                        <flux:icon.exclamation-triangle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" variant="outline" />
                        <div>
                            <flux:heading size="sm" class="text-yellow-900 dark:text-yellow-100">
                                No Visitor Found
                            </flux:heading>
                            <flux:text class="text-yellow-700 dark:text-yellow-300">
                                No visitor registered with phone number {{ $searchPhone }}
                            </flux:text>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-12 text-center">
                    <flux:icon.magnifying-glass class="mx-auto mb-3 h-16 w-16 text-zinc-300 dark:text-zinc-600" variant="outline" />
                    <flux:text class="text-zinc-500">
                        Enter a 10-digit phone number to search
                    </flux:text>
                </div>
            @endif
        </div>
    </div>

    <!-- WhatsApp Confirmation Modal -->
    <flux:modal name="send-whatsapp-confirmation" class="max-w-md">
        <form wire:submit="sendWhatsApp">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Send WhatsApp Message</flux:heading>
                    <flux:subheading>
                        Are you sure you want to send the QR code to this visitor via WhatsApp?
                    </flux:subheading>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        Send Message
                    </flux:button>
                    <flux:button type="button" wire:click="cancelSendWhatsApp" variant="ghost" class="flex-1">
                        Cancel
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
