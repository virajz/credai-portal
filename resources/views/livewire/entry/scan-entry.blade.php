<div class="flex h-full w-full flex-1 flex-col gap-6" x-data="{
    scanning: false,
    stream: null,
    startCamera() {
        this.scanning = true;
        this.$nextTick(() => {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const context = canvas.getContext('2d');

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    this.stream = stream;
                    video.srcObject = stream;
                    video.play();
                    this.scanQRCode(video, canvas, context);
                })
                .catch(err => {
                    console.error('Camera error:', err);
                    alert('Could not access camera. Please grant camera permissions.');
                    this.scanning = false;
                });
        });
    },
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        this.scanning = false;
    },
    scanQRCode(video, canvas, context) {
        if (!this.scanning) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                let uuid = code.data;

                // Extract UUID from URL if the QR code contains a full URL
                const uuidMatch = uuid.match(/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i);
                if (uuidMatch) {
                    uuid = uuidMatch[1];
                    this.stopCamera();
                    $wire.set('uuid', uuid);
                    $wire.call('searchPerson');
                    return;
                }
            }
        }

        requestAnimationFrame(() => this.scanQRCode(video, canvas, context));
    }
}" x-init="startCamera()" @restart-camera.window="startCamera()">
    <div>
        <flux:heading size="xl">Entry Scanner</flux:heading>
        <flux:subheading>Scan QR code to record entry</flux:subheading>
    </div>

    <div class="grid gap-6">
        <flux:card>
            <flux:heading size="lg">Camera Scanner</flux:heading>

            <div class="mt-4 flex flex-col gap-4">
                <div x-show="scanning" class="space-y-4">
                    <div class="relative aspect-video overflow-hidden rounded-lg border-2 border-zinc-300 dark:border-zinc-600 bg-black">
                        <video x-ref="video" class="absolute inset-0 h-full w-full object-cover"></video>
                        <canvas x-ref="canvas" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="border-2 border-white rounded-lg" style="width: 250px; height: 250px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                        </div>
                    </div>
                </div>

                <div x-show="!scanning" class="flex items-center justify-center py-12">
                    <flux:button @click="startCamera()" type="button" variant="primary" icon="camera">
                        Start Camera
                    </flux:button>
                </div>
            </div>
        </flux:card>

        @if($scannedVisitor || $scannedPartner)
            <flux:card>
                <flux:heading size="lg">{{ $personType === 'visitor' ? 'Visitor' : 'Partner' }} Details</flux:heading>

                @if($alreadyEntered)
                    <flux:callout variant="warning" icon="exclamation-triangle" class="mt-4">
                        This person has already entered today. Recording re-entry.
                    </flux:callout>
                @endif

                <div class="mt-4 space-y-4">
                    @if($scannedVisitor)
                        <div>
                            <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Name</div>
                            <div class="text-lg">{{ $scannedVisitor->name }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Phone</div>
                            <div class="text-lg">{{ $scannedVisitor->phone }}</div>
                        </div>

                        @if($scannedVisitor->company_name)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Company</div>
                                <div class="text-lg">{{ $scannedVisitor->company_name }}</div>
                            </div>
                        @endif
                    @elseif($scannedPartner)
                        <div>
                            <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Name</div>
                            <div class="text-lg">{{ $scannedPartner->first_name }} {{ $scannedPartner->last_name }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Phone</div>
                            <div class="text-lg">{{ $scannedPartner->phone }}</div>
                        </div>

                        @if($scannedPartner->firm_name)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Firm Name</div>
                                <div class="text-lg">{{ $scannedPartner->firm_name }}</div>
                            </div>
                        @endif
                    @endif

                    <flux:separator />

                    <flux:button wire:click="recordEntry" type="button" variant="primary" icon="arrow-right-end-on-rectangle" class="w-full">
                        Record Entry
                    </flux:button>
                </div>
            </flux:card>
        @endif
    </div>
</div>
