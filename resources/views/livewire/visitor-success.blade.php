<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
            <flux:icon.check class="h-8 w-8 text-green-600 dark:text-green-400" />
        </div>
        <flux:heading size="xl" class="mb-2">Registration Successful!</flux:heading>
        <flux:subheading>Thank you for registering for CREDAI Glam Property Show 2026</flux:subheading>
    </div>

    <div class="grid gap-8 md:grid-cols-2">
        <!-- Visitor Information -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Your Information</flux:heading>
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Name</div>
                    <div class="font-medium">{{ $visitor->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Phone</div>
                    <div class="font-medium">{{ $visitor->phone }}</div>
                </div>
                @if ($visitor->company_name)
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Company</div>
                        <div class="font-medium">{{ $visitor->company_name }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Registration Date</div>
                    <div class="font-medium">{{ $visitor->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Your QR Code</flux:heading>
            <flux:text class="mb-4 text-sm">
                Save this QR code for quick access to your visitor profile
            </flux:text>
            <div class="mb-4 aspect-square w-full overflow-hidden rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700">
                <div id="qr-code-svg" class="h-full w-full">
                    {!! preg_replace('/<svg/', '<svg class="w-full h-full"', $qrCodeSvg) !!}
                </div>
            </div>
            <div x-data="{
                visitorName: '{{ \Illuminate\Support\Str::slug($visitor->name) }}',
                downloadQr() {
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
                                link.download = this.visitorName + '-visitor-qr-code.png';
                                link.href = canvas.toDataURL('image/png');
                                link.click();
                            };
                            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                        }
                    }
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
        <flux:button variant="outline" href="{{ route('visitor.show', $visitor) }}" icon="user" icon:variant="outline">
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
