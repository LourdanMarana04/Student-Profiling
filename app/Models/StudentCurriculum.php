<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCurriculum extends Model
{
    protected $fillable = [
        'student_id',
        'department_id',
        'year_level',
        'semester',
        'academic_year',
        'status',
    ];

    /**
     * Get the student that owns this assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the department for this assignment.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
