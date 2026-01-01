<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'firm_name',
        'email',
        'phone',
        'areas',
        'property_types',
        'tracking_medium',
    ];

    protected function casts(): array
    {
        return [
            'areas' => 'array',
            'property_types' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Partner $partner) {
            if (empty($partner->uuid)) {
                $partner->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
