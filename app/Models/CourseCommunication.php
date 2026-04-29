<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCommunication extends Model
{
    protected $fillable = [
        'course_id', 'section_id', 'faculty_id', 'subject', 'message', 'type', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
}
