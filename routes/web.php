<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationTypeController;

// --------------------------------------------------
// Dashboard route (old student dashboard - kept for compatibility)
// --------------------------------------------------
Route::get('/students-dashboard', [StudentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('students.dashboard');

// --------------------------------------------------
// Student CRUD routes
// --------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
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

Route::middleware(['auth', 'verified'])->group(function () {
    // Donations routes
    Route::get('/', [DonationController::class, 'index'])->name('home');
    Route::get('/dashboard', [DonationController::class, 'index'])->name('dashboard');
    Route::resource('donations', DonationController::class)->except(['create','show','edit']);
    Route::get('/donations/trash', [DonationController::class, 'trash'])->name('donations.trash');
    Route::put('/donations/{id}/restore', [DonationController::class, 'restore'])->name('donations.restore');
    Route::delete('/donations/{id}/force-delete', [DonationController::class, 'forceDelete'])->name('donations.force-delete');
    Route::get('/donations/export/pdf', [DonationController::class, 'exportPdf'])->name('donations.export');
    
    // Donation Types routes
    Route::resource('donation-types', DonationTypeController::class)->parameters(['donation-types'=>'donationType'])->except(['create','show','edit']);
});

// --------------------------------------------------
// Auth routes
// --------------------------------------------------
// require __DIR__.'/auth.php';