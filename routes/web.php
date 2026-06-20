<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManagerCoordinatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDonationController;
use App\Http\Controllers\SchoolController;
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

            // PROGRAM MANAGER ROUTES
Route::middleware(['auth', 'role:Program Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return view('manager.dashboard');
    })->name('dashboard');
//school routes
    Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
//coordinator routes
    Route::get('/coordinators', [ManagerCoordinatorController::class, 'index'])->name('coordinators.index');
    Route::post('/coordinators/{id}/status', [ManagerCoordinatorController::class, 'update'])->name('coordinators.status');
//inventory routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
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
