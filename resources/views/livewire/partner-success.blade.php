<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
            <flux:icon.check class="h-8 w-8 text-green-600 dark:text-green-400" />
        </div>
        <flux:heading size="xl" class="mb-2">Registration Successful!</flux:heading>
        <flux:subheading>Thank you for registering as a partner for CREDAI Glam Property Show 2026</flux:subheading>
    </div>

    <div class="grid gap-8 md:grid-cols-2">
        <!-- Partner Information -->
        <div class="space-y-6">
            <!-- Personal Details -->
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">Personal Details</flux:heading>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Name</div>
                        <div class="font-medium">{{ $partner->first_name }} {{ $partner->last_name }}</div>
                    </div>
                    @if ($partner->firm_name)
                        <div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">Firm Name</div>
                            <div class="font-medium">{{ $partner->firm_name }}</div>
                        </div>
                    @endif
                    @if ($partner->email)
                        <div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">Email</div>
                            <div class="font-medium">{{ $partner->email }}</div>
                        </div>
                    @endif
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Phone</div>
                        <div class="font-medium">{{ $partner->phone }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Registration Date</div>
                        <div class="font-medium">{{ $partner->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Property Interests -->
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">Property Interests</flux:heading>
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-2">Property Types</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($partner->property_types as $type)
                                <flux:badge color="teal">{{ $type }}</flux:badge>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-2">Areas of Interest</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($partner->areas as $area)
                                <flux:badge color="zinc">{{ $area }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4 border-b border-zinc-200 pb-2 dark:border-zinc-700">Your QR Code</flux:heading>
            <flux:text class="mb-4 text-sm">
                Save this QR code for quick access to your partner profile. You can retrieve this anytime using your phone number.
            </flux:text>
            <div class="mb-4 w-full overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700">
                <img id="whatsapp-qr-image" src="{{ $whatsappQrImage }}" alt="Partner QR Code" class="w-full h-auto">
            </div>
            <div x-data="{
                partnerName: '{{ \Illuminate\Support\Str::slug($partner->first_name . '-' . $partner->last_name) }}',
                downloadQr() {
                    const link = document.createElement('a');
                    link.download = this.partnerName + '-partner-qr-code.png';
                    link.href = '{{ $whatsappQrImage }}';
                    link.click();
                }
            }">
                <flux:button variant="primary" @click="downloadQr" icon="arrow-down-tray" icon:variant="outline" class="w-full">
                    Download QR Code
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
        <flux:button variant="primary" href="{{ route('home') }}" icon="home" icon:variant="outline">
            Back to Home
        </flux:button>
        <flux:button variant="outline" href="{{ route('partner.show', $partner) }}" icon="user" icon:variant="outline">
            View My Profile
        </flux:button>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #qr-code-svg,
            #qr-code-svg * {
                visibility: visible;
            }
            #qr-code-svg {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
        }
    </style>
</div>
