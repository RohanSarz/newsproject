<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        // Filter by instructor (current user)
        $query->where('instructor_id', auth()->id());

        // Apply filters based on request
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return inertia('instructor/courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return inertia('instructor/courses/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'overview' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,published,archived',
            'duration' => 'nullable|integer|min:0',
            'curriculum' => 'nullable|array',
            'objectives' => 'nullable|array',
            'requirements' => 'nullable|array',
            'thumbnail' => 'nullable|string',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $course = new Course();
        $course->instructor_id = auth()->id();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->overview = $request->overview;
        $course->level = $request->level;
        $course->price = $request->price;
        $course->duration = $request->duration;
        $course->curriculum = $request->curriculum;
        $course->objectives = $request->objectives;
        $course->requirements = $request->requirements;
        $course->thumbnail = $request->thumbnail;

        // Handle scheduled publishing
        if ($request->status === 'published') {
            if ($request->publish_now) {
                // Publish immediately
                $course->published_at = now();
                $course->status = 'published';
            } elseif ($request->published_at) {
                // Schedule for future
                $course->published_at = $request->published_at;
                $course->status = 'published'; // Keep as published but with future date
            } else {
                // Published but no date set, publish now by default
                $course->published_at = now();
                $course->status = 'published';
            }
        } else {
            // Draft or archived
            $course->status = $request->status ?? 'draft';
        }

        $course->save();

        return redirect()->route('instructor.courses.show', $course->slug)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        $modules = $course->modules()->with('lessons')->get();

        $course->load('instructor');

        return inertia('instructor/courses/Show', [
            'course' => $course,
            'modules' => $modules
        ]);
    }

    public function edit(Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        return inertia('instructor/courses/Edit', [
            'course' => $course
        ]);
    }

    public function update(Request $request, Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'overview' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
            'duration' => 'nullable|integer|min:0',
            'curriculum' => 'nullable|array',
            'objectives' => 'nullable|array',
            'requirements' => 'nullable|array',
            'thumbnail' => 'nullable|string',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $course->title = $request->title;
        $course->description = $request->description;
        $course->overview = $request->overview;
        $course->level = $request->level;
        $course->price = $request->price;
        $course->duration = $request->duration;
        $course->curriculum = $request->curriculum;
        $course->objectives = $request->objectives;
        $course->requirements = $request->requirements;
        $course->thumbnail = $request->thumbnail;

        // Handle scheduled publishing
        if ($request->status === 'published') {
            if ($request->publish_now) {
                // Publish immediately
                $course->published_at = now();
                $course->status = 'published';
            } elseif ($request->published_at) {
                // Schedule for future
                $course->published_at = $request->published_at;
                $course->status = 'published';
            } else {
                // Published but no date specified
                // If already has a published_at, keep it; otherwise set to now
                if (!$course->published_at) {
                    $course->published_at = now();
                }
                $course->status = 'published';
            }
        } else {
            // Draft or archived
            $course->status = $request->status;
        }

        $course->save();

        return redirect()->route('instructor.courses.show', $course->slug)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    // Public course catalog
    public function catalog(Request $request)
    {
        $query = Course::published()->with('instructor');

        // Filters
        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('title', 'asc');
        }

        $courses = $query->paginate(12)->withQueryString();

        return inertia('courses/Catalog', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'level', 'sort']),
        ]);
    }

    // Public course detail page
    public function detail(Course $course)
    {
        // Only show published courses that are available
        if (!$course->isAvailable()) {
            abort(404);
        }

        $course->load(['instructor', 'modules.lessons']);

        $isEnrolled = auth()->check() ? auth()->user()->hasEnrolled($course->id) : false;

        return inertia('courses/Detail', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    private function authorizeCourseOwnership(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized to access this course.');
        }
    }
}