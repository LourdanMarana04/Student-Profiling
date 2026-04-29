<?php

namespace App\Policies;

use App\Models\{User, Section, GradeEntry};

class GradeBookPolicy
{
    public function manageGradeBook(User $user, Section $section): bool
    {
        return $user->isFaculty() &&
               $section->course->faculty()
                   ->where('faculty_id', $user->faculty->id)
                   ->exists();
    }

    public function updateGrade(User $user, GradeEntry $entry): bool
    {
        return $user->isFaculty() &&
               $entry->gradeComponent->faculty_id === $user->faculty->id;
    }
}
