<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyAlertSubscription extends Model
{
    protected $fillable = [
        'faculty_id',
        'notify_high_risk',
        'notify_medium_risk',
        'minimum_risk_score',
        'is_enabled',
    ];

    protected $casts = [
        'notify_high_risk' => 'boolean',
        'notify_medium_risk' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
