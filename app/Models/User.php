<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'];

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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        // Remove automatic role assignment to prevent security issues
        // Roles should be explicitly assigned during user creation
    }

    public function getRoles()
    {
        return $this->getRoleNames();
    }

    // Enrollment relationships
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('enrolled_at', 'completed_at', 'progress', 'status')
            ->withTimestamps();
    }

    public function hasEnrolled($courseId): bool
    {
        return $this->enrollments()->where('course_id', $courseId)->exists();
    }

    public function enroll(Course $course): Enrollment
    {
        return $this->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);
    }

    // Instructor relationship (courses they teach)
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}
