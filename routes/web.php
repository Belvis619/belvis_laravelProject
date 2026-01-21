<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// --------------------------------------------------
// Home route
// --------------------------------------------------
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --------------------------------------------------
// Dashboard route
// --------------------------------------------------
Route::get('/dashboard', [StudentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --------------------------------------------------
// Student CRUD routes
// --------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');
});

// --------------------------------------------------
// Course CRUD routes
// --------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

// --------------------------------------------------
// Trash Management routes
// --------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/students/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreStudent'])->name('trash.students.restore');
    Route::delete('/trash/students/{id}', [App\Http\Controllers\TrashController::class, 'forceDeleteStudent'])->name('trash.students.forceDelete');
    Route::post('/trash/courses/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreCourse'])->name('trash.courses.restore');
    Route::delete('/trash/courses/{id}', [App\Http\Controllers\TrashController::class, 'forceDeleteCourse'])->name('trash.courses.forceDelete');
});

// --------------------------------------------------
// Settings routes (Livewire 2.x)
// --------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Redirect /settings to /settings/profile
    Route::redirect('settings', 'settings/profile');

    // Register Livewire components (optional but recommended)
    Livewire::component('settings.profile', Profile::class);
    Livewire::component('settings.password', Password::class);
    Livewire::component('settings.appearance', Appearance::class);

    // Routes return Blade views that embed Livewire components
    Route::get('settings/profile', function () {
        return view('settings.profile'); // Blade view contains <livewire:settings.profile />
    })->name('settings.profile');

    Route::get('settings/password', function () {
        return view('settings.password'); // Blade view contains <livewire:settings.password />
    })->name('settings.password');

    Route::get('settings/appearance', function () {
        return view('settings.appearance'); // Blade view contains <livewire:settings.appearance />
    })->name('settings.appearance');
});

// --------------------------------------------------
// Auth routes
// --------------------------------------------------
// require __DIR__.'/auth.php';