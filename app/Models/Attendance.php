<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'schedule_id', 'student_id', 'attendance_date', 'status', 'remarks'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function schedule(): BelongsTo { return $this->belongsTo(Schedule::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
}
