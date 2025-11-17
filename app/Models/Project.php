<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
