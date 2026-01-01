<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $featuredCourses = \App\Models\Course::published()
        ->with('instructor')
        ->withCount('modules')
        ->with('modules')
        ->limit(6)
        ->get()
        ->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'level' => $course->level,
                'price' => $course->price,
                'slug' => $course->slug,
                'thumbnail' => $course->thumbnail,
                'instructor' => [
                    'id' => $course->instructor->id,
                    'name' => $course->instructor->name,
                ],
                'module_count' => $course->modules_count,
                'total_lesson_count' => $course->modules->sum(function ($module) {
                    return $module->lessons()->count();
                }),
            ];
        });

    return Inertia::render('Home', [
        'canRegister' => Features::enabled(Features::registration()),
        'featuredCourses' => $featuredCourses,
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public course catalog
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'catalog'])->name('courses.catalog');
    Route::get('/{course:slug}', [CourseController::class, 'detail'])->name('courses.detail');
});

// Student dashboard routes
Route::prefix('student')
    ->middleware(['auth', 'role:student'])
    ->group(function () {
        Route::get('/dashboard', [StudentController::class, 'index'])->name('student.dashboard');

        // Student courses
        Route::get('/courses', [EnrollmentController::class, 'myCourses'])->name('student.courses.index');

        // Learning interface
        Route::get('/courses/{course:slug}/learn', [LessonController::class, 'learn'])->name('student.courses.learn');
        Route::get('/courses/{course:slug}/modules/{module}/lessons/{lesson}', [LessonController::class, 'showLesson'])
            ->name('student.courses.lessons.show');
        Route::post('/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])
            ->name('student.lessons.progress');

        // Enrollment actions
        Route::post('/courses/{course:slug}/enroll', [EnrollmentController::class, 'enroll'])
            ->name('student.courses.enroll');
        Route::post('/courses/{course:slug}/unenroll', [EnrollmentController::class, 'unenroll'])
            ->name('student.courses.unenroll');

        // Student schedule
        Route::get('/schedule', [StudentController::class, 'schedule'])->name('student.schedule');
    });

// Instructor dashboard routes
Route::prefix('instructor')
    ->middleware(['auth', 'role:instructor'])
    ->group(function () {
        Route::get('/dashboard', [InstructorController::class, 'index'])->name('instructor.dashboard');
        Route::get('/settings', [InstructorController::class, 'settings'])->name('instructor.settings');
        Route::get('/schedule', [InstructorController::class, 'schedule'])->name('instructor.schedule');

        // Instructor courses routes - use slug for course parameter
        Route::prefix('courses')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('instructor.courses.index');
            Route::get('/create', [CourseController::class, 'create'])->name('instructor.courses.create');
            Route::post('/', [CourseController::class, 'store'])->name('instructor.courses.store');

            // Use {course:slug} for course parameter
            Route::get('/{course:slug}', [CourseController::class, 'show'])->name('instructor.courses.show');
            Route::get('/{course:slug}/edit', [CourseController::class, 'edit'])->name('instructor.courses.edit');
            Route::put('/{course:slug}', [CourseController::class, 'update'])->name('instructor.courses.update');
            Route::delete('/{course:slug}', [CourseController::class, 'destroy'])->name('instructor.courses.destroy');

            // Modules - {course:slug} but {module} uses id for scope uniqueness
            Route::prefix('{course:slug}/modules')->group(function () {
                Route::get('/', [ModuleController::class, 'index'])->name('instructor.courses.modules.index');
                Route::get('/create', [ModuleController::class, 'create'])->name('instructor.courses.modules.create');
                Route::post('/', [ModuleController::class, 'store'])->name('instructor.courses.modules.store');
                Route::get('/{module}', [ModuleController::class, 'show'])->name('instructor.courses.modules.show');
                Route::get('/{module}/edit', [ModuleController::class, 'edit'])->name('instructor.courses.modules.edit');
                Route::put('/{module}', [ModuleController::class, 'update'])->name('instructor.courses.modules.update');
                Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('instructor.courses.modules.destroy');

                // Lessons - scoped to module
                Route::prefix('{module}/lessons')->group(function () {
                    Route::get('/', [LessonController::class, 'index'])->name('instructor.courses.modules.lessons.index');
                    Route::get('/create', [LessonController::class, 'create'])->name('instructor.courses.modules.lessons.create');
                    Route::post('/', [LessonController::class, 'store'])->name('instructor.courses.modules.lessons.store');
                    Route::get('/{lesson}', [LessonController::class, 'show'])->name('instructor.courses.modules.lessons.show');
                    Route::get('/{lesson}/edit', [LessonController::class, 'edit'])->name('instructor.courses.modules.lessons.edit');
                    Route::put('/{lesson}', [LessonController::class, 'update'])->name('instructor.courses.modules.lessons.update');
                    Route::delete('/{lesson}', [LessonController::class, 'destroy'])->name('instructor.courses.modules.lessons.destroy');
                });
            });
        });
    });

Route::resource('users', UserController::class);
require __DIR__ . '/settings.php';
