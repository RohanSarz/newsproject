<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all enrollments for stats calculation (not just 3)
        $allEnrollments = $user->enrollments()->get();

        // Calculate stats
        $coursesInProgress = $allEnrollments->where('status', 'active')->count();
        $certificates = $allEnrollments->where('status', 'completed')->count();

        // Get only 3 recent enrollments with course data for display
        $enrollments = $user->enrollments()
            ->with(['course.modules'])
            ->orderBy('enrolled_at', 'desc')
            ->take(3)
            ->get();

        // Get upcoming lessons - optimized single query
        $upcomingLessons = \App\Models\Lesson::where('is_published', true)
            ->where('published_at', '>', now())
            ->whereHas('module.course.enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('module.course')
            ->orderBy('published_at', 'asc')
            ->take(5)
            ->get()
            ->map(function ($lesson) {
                return [
                    'lesson' => $lesson,
                    'module' => $lesson->module,
                    'course' => $lesson->module->course,
                    'published_at' => $lesson->published_at,
                    'days_until' => now()->diffInDays($lesson->published_at),
                ];
            });

        // Get random motivational quote from API (with timeout)
        $quote = null;
        try {
            $response = Http::timeout(2)->get(url('/api/quotes/random'));
            if ($response->successful()) {
                $quote = $response->json();
            }
        } catch (\Exception $e) {
            // Fallback quote if API fails
            $quote = [
                'quote' => 'The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice.',
                'author' => 'Brian Herbert'
            ];
        }

        return inertia('student/Dashboard', [
            'enrollments' => $enrollments,
            'stats' => [
                'totalHoursSpent' => 0,
                'coursesInProgress' => $coursesInProgress,
                'certificates' => $certificates,
            ],
            'upcomingLessons' => $upcomingLessons,
            'quote' => $quote,
        ]);
    }

    public function schedule()
    {
        $user = auth()->user();

        // Optimized single query for upcoming lessons
        $upcomingLessons = \App\Models\Lesson::where('is_published', true)
            ->where('published_at', '>', now())
            ->whereHas('module.course.enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('module.course')
            ->orderBy('published_at', 'asc')
            ->take(10)
            ->get()
            ->map(function ($lesson) {
                return [
                    'lesson' => $lesson,
                    'module' => $lesson->module,
                    'course' => $lesson->module->course,
                    'published_at' => $lesson->published_at,
                    'days_until' => now()->diffInDays($lesson->published_at),
                ];
            });

        // Optimized single query for upcoming modules
        $upcomingModules = \App\Models\Module::where('published_at', '>', now())
            ->whereHas('course.enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('course')
            ->orderBy('published_at', 'asc')
            ->take(10)
            ->get()
            ->map(function ($module) {
                return [
                    'module' => $module,
                    'course' => $module->course,
                    'published_at' => $module->published_at,
                    'days_until' => now()->diffInDays($module->published_at),
                ];
            });

        return inertia('student/schedule/Upcoming', [
            'upcomingLessons' => $upcomingLessons,
            'upcomingModules' => $upcomingModules,
        ]);
    }

    public function store(Request $request)
    {
        // Your store logic here
        return response()->json(['message' => 'Success']);
    }

    public function update(Request $request, $id)
    {
        // Your update logic here
        return response()->json(['message' => 'Updated']);
    }
}
