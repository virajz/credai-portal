<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
