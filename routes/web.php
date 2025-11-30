<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Course-related routes
Route::get('/courses/{id}', function ($id) {
    return Inertia::render('CourseDetails');
})->name('course.show');

// Student dashboard routes
Route::prefix('student')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('StudentDashboard');
    })->name('student.dashboard');

    Route::get('/courses', function () {
        return Inertia::render('StudentDashboard');
    })->name('student.courses');

    Route::get('/schedule', function () {
        return Inertia::render('StudentDashboard');
    })->name('student.schedule');

    Route::get('/community', function () {
        return Inertia::render('StudentDashboard');
    })->name('student.community');
});

// Instructor dashboard routes
Route::prefix('instructor')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('InstructorDashboard');
    })->name('instructor.dashboard');

    Route::get('/courses', function () {
        return Inertia::render('InstructorDashboard');
    })->name('instructor.courses');

    Route::get('/students', function () {
        return Inertia::render('InstructorDashboard');
    })->name('instructor.students');

    Route::get('/earnings', function () {
        return Inertia::render('InstructorDashboard');
    })->name('instructor.earnings');
});

Route::resource('users', UserController::class);
require __DIR__ . '/settings.php';
