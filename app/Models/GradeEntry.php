<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeEntry extends Model
{
    protected $fillable = ['grade_component_id', 'student_id', 'score', 'notes'];

    public function gradeComponent(): BelongsTo { return $this->belongsTo(GradeComponent::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
}
