<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'section_id', 'faculty_id', 'title',
        'description', 'points', 'due_date', 'released_at', 'status'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
    public function submissions(): HasMany { return $this->hasMany(AssignmentSubmission::class); }

    public function submittedCount(): int
    {
        return $this->submissions()
            ->whereIn('status', ['submitted', 'graded'])
            ->count();
    }

    public function pendingSubmissions()
    {
        return $this->submissions()
            ->where('status', '!=', 'graded')
            ->whereNotNull('submitted_at');
    }
}
