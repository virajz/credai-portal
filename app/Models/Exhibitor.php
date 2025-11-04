<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
