<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'course_id',
        'faculty_id',
        'section_name',
        'year_level',
        'semester',
        'capacity',
        'enrolled_count',
        'status',
    ];

    /**
     * Get the course that this section belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the faculty primarily assigned to this section.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the students enrolled in this section.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'section_student')
            ->withTimestamps();
    }

    /**
     * Get the schedules for this section.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the assignments for this section.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the grade components for this section.
     */
    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class);
    }

    /**
     * Get the attendance records for this section.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
