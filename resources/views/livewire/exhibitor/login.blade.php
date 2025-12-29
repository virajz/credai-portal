<div class="flex flex-col gap-6">
    <x-auth-header
        title="Exhibitor Login"
        description="Enter your registered phone number to receive OTP"
    />

    @if (!$otpSent)
        <form wire:submit="sendOtp" class="flex flex-col gap-6">
            <flux:field>
                <flux:label>Phone Number</flux:label>
                <flux:input
                    wire:model="phone"
                    type="text"
                    placeholder="10-digit mobile number"
                    required
                    autofocus
                    maxlength="10"
                />
                <flux:error name="phone" />
            </flux:field>

            <flux:button variant="primary" type="submit" class="w-full">
                Send OTP
            </flux:button>
        </form>
    @else
        <flux:card>
            <form wire:submit="verifyOtp" class="space-y-8">
                <div class="max-w-64 mx-auto space-y-2">
                    <flux:heading size="lg" class="text-center">Verify your account</flux:heading>
                    <flux:text class="text-center">
                        We've sent a 6-digit OTP to your WhatsApp number ending in {{ substr($phone, -4) }}.
                    </flux:text>
                </div>

                <flux:otp
                    wire:model="otp"
                    length="6"
                    label="OTP Code"
                    submit="auto"
                    class="mx-auto"
                />

                <div class="space-y-4">
                    <flux:button variant="primary" type="submit" class="w-full">
                        Verify OTP
                    </flux:button>
                    <flux:button wire:click="resendOtp" type="button" class="w-full">
                        Resend code
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif
</div>
