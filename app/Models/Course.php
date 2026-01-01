<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'title',
        'description',
        'overview',
        'slug',
        'thumbnail',
        'status',
        'level',
        'duration',
        'curriculum',
        'objectives',
        'requirements',
        'price',
        'published_at',
    ];

    protected $casts = [
        'status' => CourseStatus::class,
        'curriculum' => 'array',
        'objectives' => 'array',
        'requirements' => 'array',
        'published_at' => 'datetime',
        'duration' => 'integer',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $baseSlug = Str::slug($course->title);
                $randomSuffix = strtolower(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8));
                $course->slug = $baseSlug . '-' . $randomSuffix;
            }
        });
    }

    // Accessor for the status label
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    // Relationship with instructor (user)
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Relationship with modules
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    // Enrollment relationships
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('enrolled_at', 'progress', 'status')
            ->withTimestamps();
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    // Scope for published courses (including time-based check)
    public function scopePublished($query)
    {
        return $query->where('status', CourseStatus::PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    // Scope for scheduled courses (published but in future)
    public function scopeScheduled($query)
    {
        return $query->where('status', CourseStatus::PUBLISHED)
            ->where('published_at', '>', now());
    }

    // Scope for draft courses
    public function scopeDraft($query)
    {
        return $query->where('status', CourseStatus::DRAFT);
    }

    // Scope for archived courses
    public function scopeArchived($query)
    {
        return $query->where('status', CourseStatus::ARCHIVED);
    }

    // Scope for courses by instructor
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    // Check if course is available to students
    public function isAvailable(): bool
    {
        if ($this->status !== CourseStatus::PUBLISHED) {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        return true;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->isAvailable();
    }

    // Accessor for module count
    public function getModuleCountAttribute(): int
    {
        return $this->modules()->count();
    }

    // Accessor for total lesson count
    public function getTotalLessonCountAttribute(): int
    {
        return $this->modules()->withCount('lessons')->sum('lessons_count');
    }
}
