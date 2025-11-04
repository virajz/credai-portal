<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DraftExhibitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'resume_token',
        'brand_name',
        'office_address',
        'city',
        'contact_person_name',
        'phone_number',
        'email',
        'website',
        'video_url',
        'social_media_links',
        'facia_name',
        'current_step',
        'completed_steps',
        'is_completed',
        'last_activity_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_media_links' => 'array',
            'completed_steps' => 'array',
            'is_completed' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Generate a unique resume token
     */
    public static function generateResumeToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('resume_token', $token)->exists());

        return $token;
    }

    /**
     * Get the resume URL for this draft
     */
    public function getResumeUrlAttribute(): string
    {
        return route('exhibitor.public.register', ['resume' => $this->resume_token]);
    }
}
