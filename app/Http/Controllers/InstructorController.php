<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructorController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get instructor's courses with stats
        $courses = $user->courses()->withCount('enrollments')->get();

        // Calculate stats
        $totalStudents = $courses->sum('enrollments_count');
        $totalCourses = $courses->count();
        $publishedCourses = $courses->where('status', 'published')->count();
        $draftCourses = $courses->where('status', 'draft')->count();

        // Get recent enrollments (last 5)
        $recentEnrollments = Enrollment::whereHas('course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })
            ->with('user', 'course')
            ->orderBy('enrolled_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'name' => $enrollment->user->name,
                    'course' => $enrollment->course->title,
                    'initials' => collect(explode(' ', $enrollment->user->name))
                        ->map(fn($n) => strtoupper(substr($n, 0, 1)))
                        ->take(2)
                        ->join(''),
                    'color' => ['purple', 'blue', 'green', 'yellow'][rand(0, 3)],
                    'amount' => '+$' . $enrollment->course->price,
                    'enrolled_at' => $enrollment->enrolled_at->format('M d, Y'),
                ];
            });

        // Calculate total revenue (free courses for now)
        $totalRevenue = 0;

        // Get course performance data
        $coursePerformance = $courses->map(function ($course) {
            return [
                'title' => $course->title,
                'enrollments' => $course->enrollments_count,
                'completion_rate' => 0, // TODO: Calculate from lesson_progress
                'avg_rating' => 0, // TODO: Implement reviews
                'revenue' => 0, // Free courses
            ];
        });

        return inertia('instructor/Dashboard', [
            'stats' => [
                [
                    'title' => 'Total Courses',
                    'value' => (string) $totalCourses,
                    'change' => $publishedCourses . ' published',
                    'positive' => true,
                ],
                [
                    'title' => 'Total Students',
                    'value' => (string) $totalStudents,
                    'change' => 'Active enrollments',
                    'positive' => true,
                ],
                [
                    'title' => 'Published Courses',
                    'value' => (string) $publishedCourses,
                    'change' => $draftCourses . ' in draft',
                    'positive' => true,
                ],
                [
                    'title' => 'Draft Courses',
                    'value' => (string) $draftCourses,
                    'change' => 'Work in progress',
                    'positive' => false,
                ],
            ],
            'recentEnrollments' => $recentEnrollments,
            'coursePerformance' => $coursePerformance,
        ]);
    }

    public function settings()
    {
        return inertia('settings/Profile');
    }

    public function schedule()
    {
        $user = auth()->user();

        // Get instructor's courses with scheduled content
        $courses = $user->courses()->get();

        $scheduledModules = \App\Models\Module::whereIn('course_id', $courses->pluck('id'))
            ->where('is_published', true)
            ->where('published_at', '>', now())
            ->with('course')
            ->orderBy('published_at', 'asc')
            ->get();

        $scheduledLessons = \App\Models\Lesson::whereHas('module.course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })
            ->where('is_published', true)
            ->where('published_at', '>', now())
            ->with('module.course')
            ->orderBy('published_at', 'asc')
            ->get()
            ->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'type' => 'lesson',
                    'module' => $lesson->module->title,
                    'course' => $lesson->module->course->title,
                    'course_slug' => $lesson->module->course->slug,
                    'published_at' => $lesson->published_at,
                    'days_until' => now()->diffInDays($lesson->published_at),
                ];
            });

        $allScheduled = $scheduledModules->map(function ($module) {
            return [
                'id' => $module->id,
                'title' => $module->title,
                'type' => 'module',
                'course' => $module->course->title,
                'course_slug' => $module->course->slug,
                'published_at' => $module->published_at,
                'days_until' => now()->diffInDays($module->published_at),
            ];
        })->concat($scheduledLessons)->sortBy('published_at')->values();

        return inertia('instructor/schedule/Index', [
            'scheduledContent' => $allScheduled,
        ]);
    }
}
