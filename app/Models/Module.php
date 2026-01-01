<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'order',
        'published_at',
    ];

    protected $casts = [
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $baseSlug = Str::slug($module->title);
                $slug = $baseSlug;
                $counter = 1;

                // Check uniqueness within the same course
                while (Module::where('course_id', $module->course_id)
                    ->where('slug', $slug)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $module->slug = $slug;
            }
        });
    }

    // Relationship with course
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Relationship with lessons
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Scope for published modules (time-based check)
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', now());
        });
    }

    // Scope for scheduled modules
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    // Check if module is available to students
    public function isAvailable(): bool
    {
        // If published_at is set and is in the future, module is not available
        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        // If published_at is null or in the past, module is available
        return true;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->isAvailable();
    }

    // Accessor for lesson count
    public function getLessonCountAttribute(): int
    {
        return $this->lessons()->count();
    }
}