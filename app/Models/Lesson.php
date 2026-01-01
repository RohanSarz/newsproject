<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'description',
        'content',
        'video_url',
        'duration',
        'order',
        'is_preview',
        'published_at',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $baseSlug = Str::slug($lesson->title);
                $slug = $baseSlug;
                $counter = 1;

                // Check uniqueness within the same module
                while (Lesson::where('module_id', $lesson->module_id)
                    ->where('slug', $slug)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $lesson->slug = $slug;
            }
        });
    }

    // Relationship with module
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    // Relationship with lesson progress
    public function progress(): BelongsToMany
    {
        return $this->belongsToMany(Enrollment::class, 'lesson_progress')
            ->withPivot('completed', 'completed_at', 'watch_time')
            ->withTimestamps();
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }


    // Scope for published lessons (time-based check)
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', now());
        });
    }

    // Scope for scheduled lessons
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    // Check if lesson is available to students
    public function isAvailable(): bool
    {
        // Preview lessons are always available
        if ($this->is_preview) {
            return true;
        }

        // If published_at is set and is in the future, lesson is not available
        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        // If published_at is null or in the past, lesson is available
        return true;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->isAvailable();
    }

    // Accessor to extract YouTube video ID from URL
    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // Extract YouTube ID from various URL formats
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\n?#]+)/';
        preg_match($pattern, $this->video_url, $matches);

        return $matches[1] ?? null;
    }

    // Accessor for formatted duration
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration) {
            return null;
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}