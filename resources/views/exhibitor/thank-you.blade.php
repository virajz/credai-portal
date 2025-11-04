<x-layouts.public>
    <div class="mx-auto max-w-2xl">
        <div class="rounded-lg bg-white p-8 shadow-sm dark:bg-zinc-900">
            <div class="mb-6 flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <flux:icon.check class="size-8 text-green-600 dark:text-green-500" variant="solid" />
                </div>
            </div>

            <div class="text-center">
                <flux:heading size="xl" class="mb-3">Thank You for Registering!</flux:heading>
                <flux:subheading class="mb-6">
                    Your exhibitor registration has been completed successfully.
                </flux:subheading>

                <flux:text class="mb-8 text-zinc-600 dark:text-zinc-400">
                    We have received your registration. Our team will contact you shortly via WhatsApp on the phone
                    number you provided to discuss booth setup and logistics.
                </flux:text>

                <div class="space-y-4">
                    <flux:callout variant="info">
                        <div class="space-y-2">
                            <p class="font-semibold text-left">What's Next?</p>
                            <ul class="space-y-1 text-left list-inside list-disc text-sm">
                                <li>You'll receive a WhatsApp message from our team shortly</li>
                                <li>We'll share your exhibitor details and booth information</li>
                                <li>We'll coordinate with you on booth setup and logistics</li>
                            </ul>
                        </div>
                    </flux:callout>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                If you have any questions, please contact us at the event coordination office.
            </flux:text>
        </div>
    </div>
</x-layouts.public>
