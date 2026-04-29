<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curriculum extends Model
{
    protected $table = 'curriculum';

    protected $fillable = [
        'course_id',
        'department_id',
        'year_level',
        'semester',
        'status',
    ];

    /**
     * Get the course for this curriculum entry.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the department that owns this curriculum entry.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope active curriculum entries.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
