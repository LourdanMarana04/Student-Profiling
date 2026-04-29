<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'section_id', 'faculty_id', 'title', 'description',
        'file_path', 'file_type', 'file_size', 'published_at', 'status'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
}
