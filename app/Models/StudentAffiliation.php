<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAffiliation extends Model
{
    protected $fillable = [
        'student_id',
        'affiliation_name',
        'affiliation_type',
        'date_joined',
        'status',
    ];

    protected $casts = [
        'date_joined' => 'date',
    ];

    /**
     * Get the student that owns the affiliation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
