<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'company_name',
        'registered_number',
        'main_person_name',
        'stall_type',
        'stall_number',
        'stall_size',
        'total_payment',
        'payment_received',
        'payment_pending',
    ];

    protected function casts(): array
    {
        return [
            'total_payment' => 'decimal:2',
            'payment_received' => 'decimal:2',
            'payment_pending' => 'decimal:2',
        ];
    }

    public function exhibitors()
    {
        return $this->hasMany(Exhibitor::class);
    }

    public function draftExhibitors()
    {
        return $this->hasMany(DraftExhibitor::class);
    }
}
