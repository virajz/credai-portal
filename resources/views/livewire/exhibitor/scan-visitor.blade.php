<div class="flex h-full w-full flex-1 flex-col gap-6 p-6" x-data="{
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
                    alert('Could not access camera. Please grant camera permissions or enter UUID manually.');
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
                    $wire.set('visitorUuid', uuid);
                    $wire.call('searchVisitor');
                    return;
                }
            }
        }

        requestAnimationFrame(() => this.scanQRCode(video, canvas, context));
    }
}">
    <div>
        <flux:heading size="xl">Scan Visitor QR Code</flux:heading>
        <flux:subheading>Use your camera to scan or enter the UUID manually</flux:subheading>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <flux:card>
            <flux:heading size="lg">Scan with Camera</flux:heading>

            <div class="mt-4 flex flex-col gap-4">
                <div x-show="!scanning">
                    <flux:button @click="startCamera()" type="button" variant="primary" icon="camera" class="w-full">
                        Start Camera Scanner
                    </flux:button>
                </div>

                <div x-show="scanning" x-cloak class="space-y-4">
                    <div class="relative aspect-video overflow-hidden rounded-lg border-2 border-zinc-300 dark:border-zinc-600 bg-black">
                        <video x-ref="video" class="absolute inset-0 h-full w-full object-cover"></video>
                        <canvas x-ref="canvas" class="hidden"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="border-2 border-white rounded-lg" style="width: 250px; height: 250px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                        </div>
                    </div>
                    <flux:button @click="stopCamera()" type="button" variant="danger" class="w-full">
                        Stop Camera
                    </flux:button>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Enter UUID Manually</flux:heading>

            <form wire:submit="searchVisitor" class="mt-4 flex flex-col gap-4">
                <flux:input
                    wire:model="visitorUuid"
                    label="Visitor UUID"
                    placeholder="e.g., 550e8400-e29b-41d4-a716-446655440000"
                    required
                />
                <flux:error name="visitorUuid" />

                <flux:button type="submit" variant="primary">
                    Search Visitor
                </flux:button>
            </form>
        </flux:card>

        @if($scannedVisitor || $scannedPartner)
            <flux:card>
                <flux:heading size="lg">{{ $leadType === 'visitor' ? 'Visitor' : 'Partner' }} Details</flux:heading>

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

                        @if($scannedVisitor->age_group)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Age Group</div>
                                <div>{{ $scannedVisitor->age_group }}</div>
                            </div>
                        @endif

                        @if($scannedVisitor->interests)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Interests</div>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($scannedVisitor->interests as $interest)
                                        <flux:badge>{{ $interest }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($scannedVisitor->areas)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Preferred Areas</div>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($scannedVisitor->areas as $area)
                                        <flux:badge variant="outline">{{ $area }}</flux:badge>
                                    @endforeach
                                </div>
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

                        @if($scannedPartner->email)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Email</div>
                                <div class="text-lg">{{ $scannedPartner->email }}</div>
                            </div>
                        @endif

                        @if($scannedPartner->firm_name)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Firm Name</div>
                                <div class="text-lg">{{ $scannedPartner->firm_name }}</div>
                            </div>
                        @endif

                        @if($scannedPartner->property_types)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Property Types</div>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($scannedPartner->property_types as $type)
                                        <flux:badge color="purple">{{ $type }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($scannedPartner->areas)
                            <div>
                                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Preferred Areas</div>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($scannedPartner->areas as $area)
                                        <flux:badge variant="outline">{{ $area }}</flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <flux:separator />

                    <form wire:submit="addLead" class="flex flex-col gap-4">
                        <flux:textarea
                            wire:model="notes"
                            label="Notes (Optional)"
                            placeholder="Add any notes about this lead..."
                            rows="3"
                        />

                        <flux:button type="submit" variant="primary" icon="plus">
                            Add as Lead
                        </flux:button>
                    </form>
                </div>
            </flux:card>
        @endif
    </div>
</div>
