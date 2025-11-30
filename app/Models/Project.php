<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'exhibitor_id',
        'name',
        'area',
        'category',
        'sq_ft',
        'budget_range',
        'handover_date',
        'status',
        'pdf_path',
        'video_url',
        'usp',
        'contact_person',
        'logo_path',
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
    ];

    public function exhibitor(): BelongsTo
    {
        return $this->belongsTo(Exhibitor::class);
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

        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = $project->generateUniqueSlug();
            }
        });

        static::updating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = $project->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the project.
     */
    public function generateUniqueSlug(): string
    {
        $baseName = $this->name ?? 'project';
        $slug = Str::slug($baseName);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
