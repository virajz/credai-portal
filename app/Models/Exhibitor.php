<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'brand_name',
        'office_address',
        'city',
        'gst_number',
        'pan_number',
        'contact_person_name',
        'phone_number',
        'email',
        'website',
        'logo_path',
        'brochure_path',
        'photos',
        'video_url',
        'social_media_links',
        'facia_name',
        'additional_details',
        'stall_type',
        'stall_number',
        'stall_size',
        'total_payment',
        'payment_received',
        'payment_pending',
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
            'total_payment' => 'decimal:2',
            'payment_received' => 'decimal:2',
            'payment_pending' => 'decimal:2',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
