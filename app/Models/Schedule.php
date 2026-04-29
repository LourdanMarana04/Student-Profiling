<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'section_id',
        'faculty_id',
        'room_id',
        'laboratory_id',
        'day_of_week',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * Get the section that this schedule belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the faculty teaching this schedule.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the room for this schedule.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the laboratory for this schedule.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * Get the attendance records for this schedule.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}