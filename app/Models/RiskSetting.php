<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskSetting extends Model
{
    protected $fillable = [
        'attendance_weight',
        'violations_weight',
        'low_grades_weight',
        'incomplete_profile_weight',
        'rejected_submissions_weight',
        'high_risk_threshold',
        'medium_risk_threshold',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        return self::query()->latest('id')->firstOrCreate([], [
            'attendance_weight' => 25,
            'violations_weight' => 25,
            'low_grades_weight' => 20,
            'incomplete_profile_weight' => 20,
            'rejected_submissions_weight' => 10,
            'high_risk_threshold' => 70,
            'medium_risk_threshold' => 40,
        ]);
    }
}
