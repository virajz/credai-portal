<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate a 4-digit OTP
     */
    public function generate(string $identifier): string
    {
        $otp = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put("otp:{$identifier}", $otp, now()->addMinutes(10));

        // Log OTP to dedicated log file
        $this->logOtp($identifier, $otp);

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
     * Log OTP to dedicated file
     */
    private function logOtp(string $identifier, string $otp): void
    {
        $logPath = storage_path('logs/otp.log');

        // Ensure the logs directory exists
        if (! File::exists(dirname($logPath))) {
            File::makeDirectory(dirname($logPath), 0755, true);
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] OTP: {$otp} | Identifier: {$identifier}\n";

        File::append($logPath, $logEntry);
    }

    /**
     * Check if OTP exists for identifier
     */
    public function exists(string $identifier): bool
    {
        return Cache::has("otp:{$identifier}");
    }
}
