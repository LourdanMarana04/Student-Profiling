<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'credits',
        'department_id',
        'status',
    ];

    /**
     * Get the department that the course belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the faculty teaching this course.
     */
    public function faculty(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'course_faculty')
            ->withPivot('academic_year', 'semester')
            ->withTimestamps();
    }

    /**
     * Get the curricula that include this course.
     */
    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * Get the materials for this course.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get the assignments for this course.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the communications for this course.
     */
    public function communications(): HasMany
    {
        return $this->hasMany(CourseCommunication::class);
    }

    /**
     * Get the sections for this course.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the grade components for this course.
     */
    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class);
    }

    /**
     * Get the analytics for this course.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(FacultyAnalytics::class);
    }

    /**
     * Get the syllabus for this course.
     */
    public function syllabus()
    {
        return $this->hasOne(Syllabus::class);
    }
}
