<?php

namespace App\Livewire\Exhibitor;

use App\Models\Company;
use App\Models\ExhibitorOtp;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
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

    public function sendOtp(): void
    {
        $this->validate();

        $otpRecord = ExhibitorOtp::createForPhone($this->phone);

        Log::channel('exhibitor-otp')->info('Exhibitor OTP Generated', [
            'phone' => $this->phone,
            'otp' => $otpRecord->otp,
            'expires_at' => $otpRecord->expires_at,
        ]);

        // TODO: Integrate with WhatsApp API to send OTP
        // For now, we're just logging it for development purposes

        $this->otpSent = true;

        Flux::toast('OTP sent to your WhatsApp! Please check your messages.', variant: 'success');
    }

    public function verifyOtp(): void
    {
        $this->validate();

        $otpRecord = ExhibitorOtp::where('phone', $this->phone)
            ->where('otp', $this->otp)
            ->first();

        if (! $otpRecord) {
            $this->addError('otp', 'Invalid OTP.');

            return;
        }

        if ($otpRecord->isExpired()) {
            $this->addError('otp', 'OTP has expired. Please request a new one.');

            return;
        }

        $company = Company::where('registered_number', $this->phone)->first();

        auth('exhibitor')->login($company);

        $otpRecord->delete();

        $this->redirect(route('exhibitor.dashboard'), navigate: true);
    }

    public function resendOtp(): void
    {
        $this->otp = '';
        $this->resetErrorBag();
        $this->sendOtp();
    }

    #[Title('Exhibitor Login - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.exhibitor.login');
    }
}
