<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Get the faculty in this department.
     */
    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    /**
     * Get the students in this department through curriculums.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_curricula');
    }

    /**
     * Get the courses in this department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
