<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentIntervention extends Model
{
    protected $fillable = [
        'student_id',
        'created_by',
        'assigned_to',
        'action_type',
        'notes',
        'due_date',
        'status',
        'resolved_at',
        'outcome',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
