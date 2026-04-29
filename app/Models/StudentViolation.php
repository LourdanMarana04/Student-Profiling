<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentViolation extends Model
{
    protected $fillable = [
        'student_id',
        'violation_type',
        'description',
        'date',
        'severity',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student that owns the violation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
