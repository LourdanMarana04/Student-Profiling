<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};

class Faculty extends Model
{
    protected $table = 'faculty';

    protected $fillable = [
        'user_id',
        'faculty_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'office',
        'specialization',
        'department_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the faculty.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the faculty belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the courses taught by this faculty.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_faculty')
            ->withPivot('academic_year', 'semester')
            ->withTimestamps();
    }

    /**
     * Get the sections assigned to this faculty.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the full name of the faculty.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Determine whether the given user owns this faculty profile.
     */
    public function belongsToUser(?User $user): bool
    {
        return $user !== null && $this->user_id === $user->id;
    }

    /**
     * Get the assignments created by this faculty.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the course materials uploaded by this faculty.
     */
    public function courseMaterials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get the communications sent by this faculty.
     */
    public function communications(): HasMany
    {
        return $this->hasMany(CourseCommunication::class);
    }

    /**
     * Get the grade components created by this faculty.
     */
    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class);
    }

    /**
     * Get the analytics for this faculty.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(FacultyAnalytics::class);
    }

    /**
     * Get upcoming deadlines for assignments.
     */
    public function getUpcomingDeadlines()
    {
        return $this->assignments()
            ->where('status', 'published')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->limit(5);
    }

    /**
     * Get current semester courses.
     */
    public function getCurrentSemesterCourses()
    {
        [$academicYear, $currentSemester] = $this->currentAcademicPeriod();

        return $this->courses()
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $currentSemester);
    }

    /**
     * Get the current academic year and semester.
     *
     * @return array{0: string, 1: int}
     */
    public function currentAcademicPeriod(): array
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        $academicYear = $currentMonth >= 6
            ? $currentYear . '-' . ($currentYear + 1)
            : ($currentYear - 1) . '-' . $currentYear;
        $semester = ($currentMonth >= 6 && $currentMonth <= 12) ? 1 : 2;

        return [$academicYear, $semester];
    }

    /**
     * Build a query for students assigned to this faculty through section rosters.
     */
    public function assignedStudents()
    {
        return Student::whereHas('sections', function ($query) {
            $query->where('faculty_id', $this->id);
        })->distinct();
    }

    public function alertSubscription(): HasOne
    {
        return $this->hasOne(FacultyAlertSubscription::class);
    }
}
