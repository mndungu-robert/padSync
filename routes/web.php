<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManagerCoordinatorController;
use App\Http\Controllers\ManagerDonationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDonationController;
use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Route;

// 1. Open Public Routes
Route::get('/', [PublicDonationController::class, 'home']);

// Donation form page
Route::get('/donate', [PublicDonationController::class, 'create'])
    ->name('donate.form');
Route::get('/learn-more', [PublicDonationController::class, 'learnMore'])
    ->name('learn.more');
// Submit donation
Route::post('/donate', [PublicDonationController::class, 'store'])
    ->name('donate.store');

// 2. Base Centralized Shared Dashboard Access Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Isolated Private Role Routing Guard Enclaves
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/coordinator-status', [AdminUserController::class, 'updateCoordinatorStatus'])->name('users.coordinator-status');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/program-managers', [AdminUserController::class, 'storeProgramManager'])->name('users.program-managers.store');

    Route::get('/logs', [App\Http\Controllers\AdminUserController::class, 'indexLogs'])->name('logs');
    // Route::resource('donors', DonorController::class);
    // Route::resource('donations', DonationController::class);
});

            // PROGRAM MANAGER ROUTES
//dashboard
Route::middleware(['auth', 'role:Program Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ManagerDashboardController::class, 'index'])->name('dashboard');
//school routes
    Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
//coordinator routes
    Route::get('/coordinators', [ManagerCoordinatorController::class, 'index'])->name('coordinators.index');
    Route::post('/coordinators/{id}/status', [ManagerCoordinatorController::class, 'update'])->name('coordinators.status');
//inventory routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
//distribution routes
    Route::get('/distributions', [App\Http\Controllers\DistributionController::class, 'index'])->name('distributions.index');
    Route::post('/distributions', [App\Http\Controllers\DistributionController::class, 'store'])->name('distributions.store');
//donation routes
    Route::get('/donations', [ManagerDonationController::class, 'index'])->name('donations.index');
    Route::patch('/donations/{donation}/receive', [ManagerDonationController::class, 'markReceived'])->name('donations.receive');
// reports routes
    Route::get('/reports', [App\Http\Controllers\ManagerReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [App\Http\Controllers\ManagerReportController::class, 'export'])->name('reports.export');
});

Route::middleware(['auth', 'role:Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/dashboard', [CoordinatorController::class, 'dashboard'])->name('dashboard');

    Route::get('/enrollments', [CoordinatorController::class, 'enrollmentsIndex'])->name('enrollments.index');
    Route::post('/enrollments', [CoordinatorController::class, 'storeEnrollment'])->name('enrollments.store');

    Route::get('/shortfalls', [CoordinatorController::class, 'shortfallsIndex'])->name('shortfalls.index');
    Route::post('/shortfalls', [CoordinatorController::class, 'storeShortfall'])->name('shortfalls.store');

    Route::get('/distributions', [CoordinatorController::class, 'distributionsIndex'])->name('distributions.index');
    Route::post('/distributions/{distribution}/confirm', [CoordinatorController::class, 'confirmDistribution'])->name('distributions.confirm');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
