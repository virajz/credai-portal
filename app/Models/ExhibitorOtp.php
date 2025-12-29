<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitorOtp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function generateOtp(): string
    {
        return str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForPhone(string $phone): self
    {
        self::where('phone', $phone)->delete();

        return self::create([
            'phone' => $phone,
            'otp' => self::generateOtp(),
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
