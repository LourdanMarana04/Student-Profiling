<?php

namespace App\Policies;

use App\Models\{CourseMaterial, User, Course};
use Illuminate\Auth\Access\Response;

class CourseMaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewCourseMaterials(User $user, Course $course): bool
    {
        return $user->isFaculty() &&
               $course->faculty()->where('faculty_id', $user->faculty->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function uploadMaterials(User $user, Course $course): bool
    {
        return $user->isFaculty() &&
               $course->faculty()->where('faculty_id', $user->faculty->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseMaterial $material): bool
    {
        return $user->isFaculty() &&
               $material->faculty_id === $user->faculty->id;
    }

    /**
     * Determine whether the user can download the material.
     */
    public function downloadMaterial(User $user, CourseMaterial $material): bool
    {
        return $user->isFaculty() || $user->isStudent();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseMaterial $courseMaterial): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseMaterial $courseMaterial): bool
    {
        return false;
    }
}
