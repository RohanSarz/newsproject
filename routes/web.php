<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Home', [
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
Route::prefix('student')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    });

// Instructor dashboard routes
Route::prefix('instructor')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('instructor/Dashboard');
        })->name('instructor.dashboard');
        Route::get('/settings', function () {
            return Inertia::render('settings/Profile');
        })->name('instructor.settings');
    });

Route::resource('users', UserController::class);
require __DIR__ . '/settings.php';
