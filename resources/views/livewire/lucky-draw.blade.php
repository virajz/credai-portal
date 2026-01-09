<div class="px-4 py-20">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            @if (!$winner && !$isDrawing)
                <div class="mb-16">
                    <h1 class="text-7xl font-bold text-gray-900 mb-4 tracking-tight">Lucky Draw</h1>
                    <p class="text-2xl text-gray-600 font-light">Winner of the Hour</p>
                </div>
                <flux:button wire:click="drawWinner" variant="primary" class="px-20 py-6 text-3xl font-semibold rounded-full shadow-2xl hover:scale-105 transition-transform">
                    Draw Winner
                </flux:button>
            @endif

            @if ($isDrawing && !$winner)
                <p class="text-3xl text-gray-500 mb-16 font-light">The winner is...</p>
                <div id="name-shuffle" class="text-8xl font-bold text-gray-900 mb-8 min-h-28 tracking-tight"></div>
                <div id="phone-shuffle" class="text-5xl font-semibold text-gray-600 min-h-16"></div>
            @endif

            @if ($winner)
                <p class="text-3xl text-gray-500 mb-16 font-light">The winner is...</p>
                <div class="mb-20">
                    <p class="text-8xl font-bold text-gray-900 mb-8 tracking-tight">{{ $winner['name'] }}</p>
                    <p class="text-5xl font-semibold text-gray-600">{{ $winner['phone'] }}</p>
                </div>
                <flux:button wire:click="resetDraw" variant="outline" class="px-10 py-4 text-xl rounded-full">
                    Draw Another Winner
                </flux:button>
            @endif

            <flux:toast />
        </div>

        @if ($winners->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Previous Winners</h2>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="w-16">#</flux:table.column>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Phone</flux:table.column>
                        <flux:table.column>Drawn At</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($winners as $index => $winnerRecord)
                            <flux:table.row :key="$winnerRecord->id">
                                <flux:table.cell class="font-semibold text-zinc-500">{{ $index + 1 }}</flux:table.cell>
                                <flux:table.cell variant="strong">{{ $winnerRecord->visitor_name }}</flux:table.cell>
                                <flux:table.cell>{{ $winnerRecord->visitor_phone }}</flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">{{ $winnerRecord->drawn_at->format('d M Y, h:i A') }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($winnerRecord->is_awarded)
                                        <flux:badge color="green" size="sm" inset="top bottom">Awarded</flux:badge>
                                    @else
                                        <flux:badge color="yellow" size="sm" inset="top bottom">Pending</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if (!$winnerRecord->is_awarded)
                                        <flux:button
                                            wire:click="awardWinner({{ $winnerRecord->id }})"
                                            variant="primary"
                                            size="sm"
                                            inset="top bottom"
                                        >
                                            Award Winner
                                        </flux:button>
                                    @else
                                        <span class="text-zinc-500 text-sm">{{ $winnerRecord->awarded_at->format('h:i A') }}</span>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>

    @script
    <script>
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let nameInterval;
        let phoneInterval;

        function shuffleText(element, finalText, duration = 2500) {
            if (!element || !finalText) return;

            const startTime = Date.now();
            const interval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);

                let result = '';
                for (let i = 0; i < finalText.length; i++) {
                    if (finalText[i] === ' ') {
                        result += ' ';
                    } else if (Math.random() > progress) {
                        result += characters[Math.floor(Math.random() * characters.length)];
                    } else {
                        result += finalText[i];
                    }
                }

                element.textContent = result;

                if (progress >= 1) {
                    clearInterval(interval);
                    element.textContent = finalText;
                }
            }, 50);

            return interval;
        }

        function fireConfetti() {
            const duration = 3000;
            const end = Date.now() + duration;
            const colors = ['#22c55e', '#3b82f6', '#eab308', '#ec4899', '#8b5cf6'];

            (function frame() {
                confetti({
                    particleCount: 3,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: colors
                });
                confetti({
                    particleCount: 3,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: colors
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        }

        $wire.on('start-animation', () => {
            setTimeout(() => {
                const nameElement = document.getElementById('name-shuffle');
                const phoneElement = document.getElementById('phone-shuffle');

                if (nameElement && phoneElement && $wire.winnerName && $wire.winnerPhone) {
                    nameInterval = shuffleText(nameElement, $wire.winnerName, 2500);
                    phoneInterval = shuffleText(phoneElement, $wire.winnerPhone, 2500);

                    setTimeout(() => {
                        $wire.call('finishAnimation');

                        setTimeout(() => {
                            fireConfetti();
                        }, 200);
                    }, 2600);
                }
            }, 100);
        });

        $wire.on('no-eligible-entries', () => {
            Flux.toast({
                text: 'No eligible entries found in the last hour. Please try again later.',
                variant: 'error',
                duration: 5000,
            });
        });

        $wire.on('winner-awarded', () => {
            Flux.toast({
                text: 'Winner has been successfully awarded!',
                variant: 'success',
                duration: 3000,
            });
        });

        Livewire.hook('morph.removed', ({ el }) => {
            if (nameInterval) clearInterval(nameInterval);
            if (phoneInterval) clearInterval(phoneInterval);
        });
    </script>
    @endscript
</div>
