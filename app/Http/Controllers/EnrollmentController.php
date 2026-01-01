<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = auth()->user();

        if ($user->hasEnrolled($course->id)) {
            return back()->with('info', 'You are already enrolled in this course.');
        }

        $user->enroll($course);

        return redirect()->route('student.courses.learn', $course->slug)
            ->with('success', 'Successfully enrolled in ' . $course->title);
    }

    public function unenroll(Course $course)
    {
        $user = auth()->user();

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();

        return back()->with('success', 'You have unenrolled from this course.');
    }

    public function myCourses()
    {
        $user = auth()->user();
        $enrollments = $user->enrollments()
            ->with('course.modules')
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        return inertia('student/courses/MyCourses', [
            'enrollments' => $enrollments,
        ]);
    }
}
