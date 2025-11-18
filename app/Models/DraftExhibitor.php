<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftExhibitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'office_address',
        'city',
        'gst_number',
        'pan_number',
        'email',
        'website',
        'video_url',
        'social_media_links',
        'facia_name',
        'additional_details',
        'extra_furniture_details',
        'exhibitor_passes_details',
        'momento_name',
        'car_pass_details',
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
