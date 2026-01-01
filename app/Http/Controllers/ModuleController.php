<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        $modules = $course->modules()->with('lessons')->get();

        return inertia('instructor/courses/Modules/Index', [
            'course' => $course,
            'modules' => $modules
        ]);
    }

    public function create(Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        return inertia('instructor/courses/Modules/Create', [
            'course' => $course
        ]);
    }

    public function store(Request $request, Course $course)
    {
        // Authorize that the current user owns this course
        $this->authorizeCourseOwnership($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $module = new Module();
        $module->course_id = $course->id;
        $module->title = $request->title;
        $module->description = $request->description;
        $module->order = $request->order;

        // Handle scheduled publishing
        if ($request->publish_now) {
            $module->published_at = now();
        } elseif ($request->published_at) {
            $module->published_at = $request->published_at;
        }

        $module->save();

        return redirect()->route('instructor.courses.modules.index', $course->slug)
            ->with('success', 'Module created successfully.');
    }

    public function show(Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        return inertia('instructor/courses/Modules/Show', [
            'course' => $course,
            'module' => $module->load('lessons')
        ]);
    }

    public function edit(Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        return inertia('instructor/courses/Modules/Edit', [
            'course' => $course,
            'module' => $module
        ]);
    }

    public function update(Request $request, Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'publish_now' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $module->title = $request->title;
        $module->description = $request->description;
        $module->order = $request->order;

        // Handle scheduled publishing
        if ($request->publish_now) {
            $module->published_at = now();
        } elseif ($request->published_at) {
            $module->published_at = $request->published_at;
        }

        $module->save();

        return redirect()->route('instructor.courses.modules.index', $course->slug)
            ->with('success', 'Module updated successfully.');
    }

    public function destroy(Course $course, Module $module)
    {
        // Authorize that the current user owns this course and module
        $this->authorizeCourseOwnership($course);
        $this->authorizeModuleOwnership($module, $course);

        $module->delete();

        return redirect()->route('instructor.courses.modules.index', $course->slug)
            ->with('success', 'Module deleted successfully.');
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
}