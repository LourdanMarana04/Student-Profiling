<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicHistory extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'semester',
        'grade',
        'units',
    ];

    /**
     * Get the student that owns the academic history.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for this academic history.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
