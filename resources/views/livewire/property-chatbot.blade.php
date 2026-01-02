<div>
    <!-- Chat Button (Fixed Position) -->
    <button wire:click="toggleChat" type="button"
        class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition-all hover:bg-zinc-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
        @if ($isOpen)
            <x-heroicon-o-x-mark class="h-6 w-6" />
        @else
            <x-heroicon-o-chat-bubble-left-right class="h-6 w-6" />
        @endif
    </button>

    <!-- Chat Window -->
    @if ($isOpen)
        <div
            class="fixed bottom-24 right-6 z-50 flex h-[600px] w-full max-w-md flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-900 dark:bg-zinc-100">
                        <x-heroicon-o-home class="h-5 w-5 text-white dark:text-zinc-900" />
                    </div>
                    <div>
                        <h3 class="text-sm font-normal text-zinc-900 dark:text-white">Property Assistant</h3>
                        <p class="text-xs font-light text-zinc-500 dark:text-zinc-400">Ask about properties</p>
                    </div>
                </div>
                <button wire:click="clearChat" type="button"
                    class="rounded-lg p-2 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300">
                    <x-heroicon-o-arrow-path class="h-4 w-4" />
                </button>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 space-y-4 overflow-y-auto bg-white px-6 py-4 dark:bg-zinc-900" x-data="{
                scrollToBottom() {
                    this.$nextTick(() => {
                        $el.scrollTop = $el.scrollHeight
                    })
                }
            }"
                x-init="scrollToBottom()" x-effect="scrollToBottom()" @message-sent.window="scrollToBottom()">
                @foreach ($messages as $msg)
                    @if ($msg['role'] === 'assistant')
                        <!-- Assistant Message -->
                        <div class="flex gap-3">
                            <div
                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <x-heroicon-o-sparkles class="h-4 w-4 text-zinc-600 dark:text-zinc-400" />
                            </div>
                            <div class="flex-1 space-y-3">
                                <!-- Text Response -->
                                @if (!empty($msg['content']))
                                    <div
                                        class="prose prose-sm max-w-none rounded-2xl rounded-tl-none bg-zinc-50 px-4 py-3 text-sm font-light text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:prose-invert">
                                        {!! nl2br(e($msg['content'])) !!}
                                    </div>
                                @endif

                                <!-- Property Cards -->
                                @if (isset($msg['properties']) && !empty($msg['properties']) && is_array($msg['properties']))
                                    <div class="space-y-1.5">
                                        @foreach ($msg['properties'] as $property)
                                            <a href="{{ $property['url'] }}" target="_blank"
                                                class="block rounded-lg border border-zinc-200 bg-white p-2.5 transition-all hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-baseline gap-2">
                                                            <h4
                                                                class="text-xs font-medium text-zinc-900 dark:text-white truncate">
                                                                {{ $property['name'] }}
                                                            </h4>
                                                            @if ($property['status'])
                                                                <span
                                                                    class="inline-flex items-center rounded-full bg-zinc-100 px-1.5 py-0.5 text-[10px] font-light text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400 whitespace-nowrap">
                                                                    {{ $property['status'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="mt-0.5 flex items-center gap-3 text-[11px] text-zinc-500 dark:text-zinc-400">
                                                            <span class="flex items-center gap-1">
                                                                <x-heroicon-o-map-pin class="h-3 w-3" />
                                                                {{ $property['area'] }}
                                                            </span>

                                                            @php
                                                                // For Residential/Commercial: get data from units
                                                                // For Plotting/Weekend Home: get data from main columns
                                                                $hasUnits =
                                                                    !empty($property['units']) &&
                                                                    is_array($property['units']);
                                                                $budget = $property['budget_range'] ?? null;
                                                                $size = $property['sq_ft'] ?? null;
                                                                $allBedrooms = [];

                                                                if ($hasUnits) {
                                                                    foreach ($property['units'] as $unit) {
                                                                        if (!empty($unit['budget'])) {
                                                                            $budget = $unit['budget'];
                                                                        }
                                                                        if (!empty($unit['area'])) {
                                                                            $size = $unit['area'];
                                                                        }
                                                                        if (!empty($unit['bedrooms'])) {
                                                                            $allBedrooms[] = $unit['bedrooms'];
                                                                        }
                                                                    }
                                                                    $allBedrooms = array_unique($allBedrooms);
                                                                    sort($allBedrooms);
                                                                }
                                                            @endphp

                                                            @if (!empty($allBedrooms))
                                                                <span class="flex items-center gap-1">
                                                                    <x-heroicon-o-home class="h-3 w-3" />
                                                                    {{ implode(', ', $allBedrooms) }}
                                                                </span>
                                                            @endif

                                                            @if ($size)
                                                                <span class="flex items-center gap-1">
                                                                    <x-heroicon-o-square-3-stack-3d class="h-3 w-3" />
                                                                    {{ $size }} sq.ft
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <x-heroicon-o-arrow-top-right-on-square
                                                        class="h-3.5 w-3.5 flex-shrink-0 text-zinc-400" />
                                                </div>
                                            </a>
                                        @endforeach

                                        @if (isset($msg['has_more']) && $msg['has_more'])
                                            <a href="{{ route('projects.index', ['search' => $msg['search_message'] ?? '']) }}"
                                                target="_blank"
                                                class="mt-2 block rounded-lg border border-zinc-900 bg-zinc-900 px-4 py-2.5 text-center text-xs font-medium text-white transition-all hover:bg-zinc-800 dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                                                View All {{ $msg['total_count'] }} Results →
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- User Message -->
                        <div class="flex justify-end gap-3">
                            <div class="flex-1 text-right">
                                <div
                                    class="inline-block rounded-2xl rounded-tr-none bg-zinc-900 px-4 py-3 text-sm font-light text-white dark:bg-zinc-100 dark:text-zinc-900">
                                    {{ $msg['content'] }}
                                </div>
                            </div>
                            <div
                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-zinc-900 dark:bg-zinc-100">
                                <x-heroicon-o-user class="h-4 w-4 text-white dark:text-zinc-900" />
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Loading Indicator with Thinking Animation -->
                <div wire:loading wire:target="sendMessage" class="flex gap-3">
                    <div
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <svg class="h-4 w-4 animate-spin text-zinc-600 dark:text-zinc-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="rounded-2xl rounded-tl-none bg-zinc-50 px-4 py-3 dark:bg-zinc-800">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-light text-zinc-600 dark:text-zinc-400">Thinking</span>
                                <div class="flex items-center gap-1">
                                    <div
                                        class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400 [animation-delay:-0.3s]">
                                    </div>
                                    <div
                                        class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400 [animation-delay:-0.15s]">
                                    </div>
                                    <div class="h-1.5 w-1.5 animate-bounce rounded-full bg-zinc-400"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t border-zinc-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <input wire:model="message" type="text" placeholder="Ask about properties..."
                        class="flex-1 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-light text-zinc-900 placeholder-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-100 dark:focus:ring-zinc-100"
                        @disabled($isLoading) />
                    <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage"
                        class="flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2.5 text-white transition-colors hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                    </button>
                </form>
                <p class="mt-2 text-xs font-light text-zinc-400 dark:text-zinc-500">
                    Try: "I need a home in Vesu area, budget 50L"
                </p>
            </div>
        </div>
    @endif
</div>
