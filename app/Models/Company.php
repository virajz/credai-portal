<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'company_name',
        'registered_number',
        'main_person_name',
        'stall_type',
        'stall_number',
        'stall_size',
        'registration_token',
        'has_submitted',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'has_submitted' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Generate a unique registration token for the company
     */
    public static function generateRegistrationToken(): string
    {
        do {
            $token = \Illuminate\Support\Str::random(32);
        } while (self::where('registration_token', $token)->exists());

        return $token;
    }

    /**
     * Get the registration URL for this company
     */
    public function getRegistrationUrlAttribute(): string
    {
        return route('client.register', ['token' => $this->registration_token]);
    }

    /**
     * Mark this company as having submitted their form
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'has_submitted' => true,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Check if the company can still register
     */
    public function canRegister(): bool
    {
        return ! $this->has_submitted;
    }

    public function exhibitor()
    {
        return $this->hasOne(Exhibitor::class);
    }

    public function draftExhibitor()
    {
        return $this->hasOne(DraftExhibitor::class);
    }

    /**
     * Legacy relationship - kept for backwards compatibility
     */
    public function exhibitors()
    {
        return $this->hasMany(Exhibitor::class);
    }

    public function draftExhibitors()
    {
        return $this->hasMany(DraftExhibitor::class);
    }
}
