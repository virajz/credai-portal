<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exhibitor extends Model
{
    /** @use HasFactory<\Database\Factories\ExhibitorFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'company_id',
        'office_address',
        'city',
        'gst_number',
        'pan_number',
        'email',
        'website',
        'logo_path',
        'brochure_path',
        'photos',
        'video_url',
        'social_media_links',
        'facia_name',
        'additional_details',
        'extra_furniture_details',
        'exhibitor_passes_details',
        'momento_name',
        'car_pass_details',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'social_media_links' => 'array',
        ];
    }

    /**
     * Get the brand name from the company
     */
    public function getBrandNameAttribute(): string
    {
        return $this->company->company_name ?? '';
    }

    /**
     * Get the contact person name from the company
     */
    public function getContactPersonNameAttribute(): string
    {
        return $this->company->main_person_name ?? '';
    }

    /**
     * Get the phone number from the company
     */
    public function getPhoneNumberAttribute(): string
    {
        return $this->company->registered_number ?? '';
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Exhibitor $exhibitor) {
            if (empty($exhibitor->slug)) {
                $exhibitor->slug = $exhibitor->generateUniqueSlug();
            }
        });

        static::updating(function (Exhibitor $exhibitor) {
            if (empty($exhibitor->slug)) {
                $exhibitor->slug = $exhibitor->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the exhibitor.
     */
    public function generateUniqueSlug(): string
    {
        $baseName = $this->company?->company_name ?? 'exhibitor';
        $slug = Str::slug($baseName);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }
}
