<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'submission_text', 'file_path',
        'submitted_at', 'is_late', 'score', 'feedback', 'graded_at', 'status'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }

    public function isLate(): bool
    {
        return $this->submitted_at?->isAfter($this->assignment->due_date) ?? false;
    }
}
