<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class GradeComponent extends Model
{
    protected $fillable = [
        'course_id', 'section_id', 'faculty_id', 'name', 'weight', 'total_points', 'sort_order'
    ];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
    public function gradeEntries(): HasMany { return $this->hasMany(GradeEntry::class); }
}
