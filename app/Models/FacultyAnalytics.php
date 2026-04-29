<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyAnalytics extends Model
{
    protected $fillable = [
        'faculty_id', 'course_id', 'section_id', 'academic_year', 'semester',
        'total_students', 'avg_attendance_count', 'avg_attendance_rate',
        'avg_class_grade', 'assignment_completion_rate', 'student_engagement_score', 'insights'
    ];

    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
}
