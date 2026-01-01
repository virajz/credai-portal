<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_name',
        'category',
        'registered_number',
        'main_person_name',
        'stall_type',
        'stall_number',
        'stall_size',
        'registration_token',
        'has_submitted',
        'submitted_at',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'has_submitted' => 'boolean',
            'submitted_at' => 'datetime',
            'is_locked' => 'boolean',
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
        return ! $this->has_submitted && ! $this->is_locked;
    }

    /**
     * Lock the registration link
     */
    public function lockRegistration(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the registration link
     */
    public function unlockRegistration(): void
    {
        $this->update(['is_locked' => false]);
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

    public function leads()
    {
        return $this->hasMany(ExhibitorLead::class);
    }

    public function leadVisitors()
    {
        return $this->belongsToMany(Visitor::class, 'exhibitor_leads')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function partnerLeads()
    {
        return $this->hasMany(PartnerLead::class);
    }

    public function leadPartners()
    {
        return $this->belongsToMany(Partner::class, 'partner_leads')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Company $company) {
            if (empty($company->uuid)) {
                $company->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the last 6 digits of the UUID
     */
    public function getUuidLast6Attribute(): string
    {
        return substr(str_replace('-', '', $this->uuid), -6);
    }

    /**
     * Get the WhatsApp inquiry URL for this company
     */
    public function getWhatsappInquiryUrlAttribute(): string
    {
        $phoneNumber = '919998077280';
        $message = urlencode("Hi, I want to know more about {$this->company_name} - {$this->uuid_last_6}");

        return "https://wa.me/{$phoneNumber}?text={$message}";
    }

    /**
     * Get the QR validation URL for this company
     */
    public function getQrValidationUrlAttribute(): string
    {
        return route('exhibitor.validate', ['uuid' => $this->uuid]);
    }
}
