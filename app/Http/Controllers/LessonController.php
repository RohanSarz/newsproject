<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index(Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        $lessons = $module->lessons()->get();

        return inertia('instructor/courses/Modules/Lessons/Index', [
            'course' => $course,
            'module' => $module,
            'lessons' => $lessons
        ]);
    }

    public function create(Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        return inertia('instructor/courses/Modules/Lessons/Create', [
            'course' => $course,
            'module' => $module
        ]);
    }

    public function store(Request $request, Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $lesson = new Lesson();
        $lesson->module_id = $module->id;
        $lesson->title = $request->title;
        $lesson->description = $request->description;
        $lesson->content = $request->content;
        $lesson->video_url = $request->video_url;
        $lesson->duration = $request->duration;
        $lesson->order = $request->order;
        $lesson->is_preview = $request->is_preview ?? false;

        // Handle scheduled publishing
        if ($request->publish_now) {
            $lesson->published_at = now();
        } elseif ($request->published_at) {
            $lesson->published_at = $request->published_at;
        }

        $lesson->save();

        return redirect()->route('instructor.courses.modules.lessons.index', [
            'course' => $course->slug,
            'module' => $module->id
        ])->with('success', 'Lesson created successfully.');
    }

    public function show(Course $course, Module $module, Lesson $lesson)
    {
        // Authorize that the current user owns this course, module, and lesson
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);
        $this->authorizeLessonOwnership($lesson, $module);

        return inertia('instructor/courses/Modules/Lessons/Show', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson
        ]);
    }

    public function edit(Course $course, Module $module, Lesson $lesson)
    {
        // Authorize that the current user owns this course, module, and lesson
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);
        $this->authorizeLessonOwnership($lesson, $module);

        return inertia('instructor/courses/Modules/Lessons/Edit', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson
        ]);
    }

    public function update(Request $request, Course $course, Module $module, Lesson $lesson)
    {
        // Authorize that the current user owns this course, module, and lesson
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);
        $this->authorizeLessonOwnership($lesson, $module);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $lesson->title = $request->title;
        $lesson->description = $request->description;
        $lesson->content = $request->content;
        $lesson->video_url = $request->video_url;
        $lesson->duration = $request->duration;
        $lesson->order = $request->order;
        $lesson->is_preview = $request->is_preview ?? false;

        // Handle scheduled publishing
        if ($request->publish_now) {
            $lesson->published_at = now();
        } elseif ($request->published_at) {
            $lesson->published_at = $request->published_at;
        }

        $lesson->save();

        return redirect()->route('instructor.courses.modules.lessons.index', [
            'course' => $course->slug,
            'module' => $module->id
        ])->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, Module $module, Lesson $lesson)
    {
        // Authorize that the current user owns this course, module, and lesson
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);
        $this->authorizeLessonOwnership($lesson, $module);

        $lesson->delete();

        return redirect()->route('instructor.courses.modules.lessons.index', [
            'course' => $course->slug,
            'module' => $module->id
        ])->with('success', 'Lesson deleted successfully.');
    }

    // Student learning methods
    public function learn(Course $course)
    {
        $user = auth()->user();

        if (!$user->hasEnrolled($course->id)) {
            return redirect()->route('courses.detail', $course->slug)
                ->with('error', 'Please enroll in this course first.');
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $course->load(['modules' => function ($query) use ($user, $enrollment) {
            $query->with(['lessons' => function ($q) use ($user, $enrollment) {
                $q->with(['lessonProgress' => function ($progressQuery) use ($enrollment) {
                    $progressQuery->where('enrollment_id', $enrollment->id);
                }]);
            }]);
        }]);

        // Get first incomplete lesson or first lesson
        $firstModule = $course->modules->first();
        $firstLesson = $firstModule ? $firstModule->lessons->first() : null;

        if (!$firstLesson) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No lessons available yet.');
        }

        return inertia('student/courses/Learn', [
            'course' => $course,
            'enrollment' => $enrollment,
            'currentModule' => $firstModule,
            'currentLesson' => $firstLesson,
        ]);
    }

    public function showLesson(Course $course, Module $module, Lesson $lesson)
    {
        $user = auth()->user();

        if (!$user->hasEnrolled($course->id)) {
            abort(403, 'You must enroll in this course first.');
        }

        // Removed availability check - show all lessons

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $course->load(['modules' => function ($query) use ($user, $enrollment) {
            $query->with(['lessons' => function ($q) use ($user, $enrollment) {
                $q->with(['lessonProgress' => function ($progressQuery) use ($enrollment) {
                    $progressQuery->where('enrollment_id', $enrollment->id);
                }]);
            }]);
        }]);

        return inertia('student/courses/Learn', [
            'course' => $course,
            'enrollment' => $enrollment,
            'currentModule' => $module,
            'currentLesson' => $lesson,
        ]);
    }

    public function updateProgress(Request $request, Lesson $lesson)
    {
        $request->validate([
            'completed' => 'boolean',
            'watch_time' => 'integer',
        ]);

        $user = auth()->user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        $progress = LessonProgress::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed' => false,
                'watch_time' => 0,
            ]
        );

        $progress->completed = $request->completed;
        $progress->watch_time = $request->watch_time;
        $progress->last_watched_at = now();

        if ($request->completed && !$progress->completed_at) {
            $progress->completed_at = now();
        }

        $progress->save();

        // Update overall enrollment progress
        $this->calculateEnrollmentProgress($enrollment);

        return back()->with('success', 'Progress updated.');
    }

    private function calculateEnrollmentProgress(Enrollment $enrollment)
    {
        $course = $enrollment->course;
        $totalLessons = $course->modules->sum(function ($module) {
            return $module->lessons()->count();
        });

        $completedLessons = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('completed', true)
            ->count();

        $progress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        $enrollment->progress = round($progress);
        $enrollment->completed_at = $progress === 100 ? now() : null;
        $enrollment->status = $progress === 100 ? 'completed' : 'active';
        $enrollment->save();
    }

    private function authorizeCourseOwnership(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized to access this course.');
        }
    }

    private function authorizeModuleOwnership(Module $module, Course $course)
    {
        if ($module->course_id !== $course->id) {
            abort(403, 'Unauthorized to access this module.');
        }
    }

    private function authorizeLessonOwnership(Lesson $lesson, Module $module)
    {
        if ($lesson->module_id !== $module->id) {
            abort(403, 'Unauthorized to access this lesson.');
        }
    }
}