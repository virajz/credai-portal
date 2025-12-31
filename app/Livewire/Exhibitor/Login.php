<?php

namespace App\Livewire\Exhibitor;

use App\Models\Company;
use App\Services\OtpService;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    public string $phone = '';

    public string $otp = '';

    public bool $otpSent = false;

    public function mount(): void
    {
        // Redirect to dashboard if already logged in
        if (auth('exhibitor')->check()) {
            $this->redirect(route('exhibitor.dashboard'), navigate: true);
        }
    }

    public function rules(): array
    {
        if (! $this->otpSent) {
            return [
                'phone' => 'required|digits:10|exists:companies,registered_number',
            ];
        }

        return [
            'otp' => 'required|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => 'No company found with this phone number.',
        ];
    }

    public function sendOtp(OtpService $otpService): void
    {
        $this->validate();

        $company = Company::where('registered_number', $this->phone)->first();

        $otpService->generateAndSend(
            phoneNumber: $this->phone,
            name: $company->company_name ?? 'Exhibitor'
        );

        $this->otpSent = true;

        Flux::toast('OTP sent to your WhatsApp! Please check your messages.', variant: 'success');
    }

    public function verifyOtp(OtpService $otpService): void
    {
        $this->validate();

        if (! $otpService->verify($this->phone, $this->otp)) {
            $this->addError('otp', 'Invalid or expired OTP. Please request a new one.');

            return;
        }

        $company = Company::where('registered_number', $this->phone)->first();

        auth('exhibitor')->login($company);

        $this->redirect(route('exhibitor.dashboard'), navigate: true);
    }

    public function resendOtp(): void
    {
        $this->otp = '';
        $this->resetErrorBag();
        $this->sendOtp(app(OtpService::class));
    }

    #[Title('Exhibitor Login - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.exhibitor.login');
    }
}
