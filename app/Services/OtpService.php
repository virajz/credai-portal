<?php

namespace App\Services;

use App\Jobs\SendOtpMessage;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    /**
     * Generate a 6-digit OTP
     */
    public function generate(string $identifier): string
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put("otp:{$identifier}", $otp, now()->addMinutes(10));

        return $otp;
    }

    /**
     * Generate and dispatch OTP via WhatsApp job
     */
    public function generateAndSend(string $phoneNumber, string $name = 'User'): string
    {
        $otp = $this->generate($phoneNumber);

        SendOtpMessage::dispatch(
            name: $name,
            phoneNumber: $phoneNumber,
            otp: $otp
        );

        return $otp;
    }

    /**
     * Verify the OTP
     */
    public function verify(string $identifier, string $otp): bool
    {
        $cachedOtp = Cache::get("otp:{$identifier}");

        if ($cachedOtp && $cachedOtp === $otp) {
            // Clear the OTP after successful verification
            Cache::forget("otp:{$identifier}");

            return true;
        }

        return false;
    }

    /**
     * Check if OTP exists for identifier
     */
    public function exists(string $identifier): bool
    {
        return Cache::has("otp:{$identifier}");
    }
}
