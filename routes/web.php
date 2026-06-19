<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDonationController;
use Illuminate\Support\Facades\Route;

// 1. Open Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Donation form page
Route::get('/donate', [PublicDonationController::class, 'create'])
    ->name('donate.form');
// Submit donation
Route::post('/donate', [PublicDonationController::class, 'store'])
    ->name('donate.store');

// 2. Base Centralized Shared Dashboard Access Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Isolated Private Role Routing Guard Enclaves
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/coordinator-status', [AdminUserController::class, 'updateCoordinatorStatus'])->name('users.coordinator-status');
    Route::post('/users/program-managers', [AdminUserController::class, 'storeProgramManager'])->name('users.program-managers.store');

    // Route::resource('donors', DonorController::class);
    // Route::resource('donations', DonationController::class);
});

Route::middleware(['auth', 'role:Program Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return view('manager.dashboard');
    })->name('dashboard');

    Route::get('/schools', function () {
        return view('manager.schools.index');
    })->name('schools.index');

    Route::get('/coordinators', function () {
        return view('manager.coordinators.index');
    })->name('coordinators.index');

    Route::get('/inventory', function () {
        return view('manager.inventory.index');
    })->name('inventory.index');
});

Route::middleware(['auth', 'role:Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/dashboard', function () {
        return view('coordinator.dashboard');
    })->name('dashboard');

    Route::get('/enrollments', function () {
        return view('coordinator.enrollments.index');
    })->name('enrollments.index');

    Route::get('/shortfalls', function () {
        return view('coordinator.shortfalls.index');
    })->name('shortfalls.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
