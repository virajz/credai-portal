<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'age_group',
        'current_residential_area',
        'company_name',
        'interests',
        'residential_types',
        'commercial_types',
        'plotting_types',
        'weekend_home_types',
        'planning_to_buy',
        'areas',
        'tracking_medium',
    ];

    protected function casts(): array
    {
        return [
            'interests' => 'array',
            'residential_types' => 'array',
            'commercial_types' => 'array',
            'plotting_types' => 'array',
            'weekend_home_types' => 'array',
            'areas' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Visitor $visitor) {
            if (empty($visitor->uuid)) {
                $visitor->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
