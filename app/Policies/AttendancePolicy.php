<?php

namespace App\Policies;

use App\Models\{User, Schedule, Section, Attendance};

class AttendancePolicy
{
    public function recordAttendance(User $user, Schedule $schedule): bool
    {
        return $user->isFaculty() &&
               $user->faculty->id === $schedule->faculty_id;
    }

    public function viewAttendance(User $user, Section $section): bool
    {
        return $user->isFaculty() &&
               $section->course->faculty()
                   ->where('faculty_id', $user->faculty->id)
                   ->exists();
    }
}
