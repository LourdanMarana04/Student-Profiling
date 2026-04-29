<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use App\Models\User;
use App\Models\Department;
use App\Models\StudentCurriculum;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'phone',
        'email',
        'photo_path',
        'year_level',
        'section',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student's academic history.
     */
    public function academicHistories(): HasMany
    {
        return $this->hasMany(StudentAcademicHistory::class);
    }

    /**
     * Get the student's activities.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(StudentActivity::class);
    }

    /**
     * Get the student's violations.
     */
    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    /**
     * Get the student's curriculum assignments.
     */
    public function curriculums(): HasMany
    {
        return $this->hasMany(StudentCurriculum::class);
    }

    /**
     * Get the student's department through curriculums.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the student's skills.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(StudentSkill::class);
    }

    /**
     * Get the student's affiliations.
     */
    public function affiliations(): HasMany
    {
        return $this->hasMany(StudentAffiliation::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(StudentIntervention::class);
    }

    public function correctionRequests(): HasMany
    {
        return $this->hasMany(StudentCorrectionRequest::class);
    }

    /**
     * Get the sections assigned to the student.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_student')
            ->withTimestamps();
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Build the student's profile completion checklist.
     *
     * @return array<int, array{label: string, completed: bool}>
     */
    public function profileCompletionChecklist(): array
    {
        return [
            ['label' => 'Account name', 'completed' => filled($this->user?->name)],
            ['label' => 'Email address', 'completed' => filled($this->email ?: $this->user?->email)],
            ['label' => 'Student ID', 'completed' => filled($this->student_id)],
            ['label' => 'First name', 'completed' => filled($this->first_name)],
            ['label' => 'Last name', 'completed' => filled($this->last_name)],
            ['label' => 'Date of birth', 'completed' => ! is_null($this->date_of_birth)],
            ['label' => 'Gender', 'completed' => filled($this->gender)],
            ['label' => 'Phone number', 'completed' => filled($this->phone)],
            ['label' => 'Address', 'completed' => filled($this->address)],
            ['label' => 'Year level', 'completed' => ! is_null($this->year_level)],
            ['label' => 'Section', 'completed' => filled($this->section)],
            ['label' => 'Profile photo', 'completed' => filled($this->photo_path)],
            ['label' => 'At least one skill', 'completed' => $this->relationCount('skills') > 0],
            ['label' => 'At least one activity', 'completed' => $this->relationCount('activities') > 0],
            ['label' => 'At least one affiliation', 'completed' => $this->relationCount('affiliations') > 0],
        ];
    }

    public function profileCompletionPercentage(): int
    {
        $checklist = $this->profileCompletionChecklist();
        $total = count($checklist);

        if ($total === 0) {
            return 0;
        }

        $completed = collect($checklist)->where('completed', true)->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * @return list<string>
     */
    public function incompleteProfileItems(): array
    {
        return collect($this->profileCompletionChecklist())
            ->where('completed', false)
            ->pluck('label')
            ->values()
            ->all();
    }

    private function relationCount(string $relation): int
    {
        if ($this->relationLoaded($relation)) {
            return $this->{$relation}->count();
        }

        return $this->{$relation}()->count();
    }

    /**
     * Determine whether the given user owns this student profile.
     */
    public function belongsToUser(?User $user): bool
    {
        return $user !== null && $this->user_id === $user->id;
    }
}
