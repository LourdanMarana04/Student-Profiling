<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_STUDENT = 'student';
    public const ROLE_FACULTY = 'faculty';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Roles that can be managed directly from the user management screen.
     *
     * @return list<string>
     */
    public static function manageableRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_STAFF,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Check if user can manage student records.
     */
    public function canManageStudents(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Check if user is faculty
     */
    public function isFaculty(): bool
    {
        return $this->role === self::ROLE_FACULTY;
    }

    /**
     * Check if user can manage faculty records.
     */
    public function canManageFaculty(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    /**
     * Get the student profile linked to this user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the faculty profile linked to this user.
     */
    public function faculty(): HasOne
    {
        return $this->hasOne(Faculty::class);
    }

    public function hasManagedProfile(): bool
    {
        return $this->student()->exists() || $this->faculty()->exists();
    }
}
