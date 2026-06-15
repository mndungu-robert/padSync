<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
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
    Route::resource('donors', DonorController::class);
    Route::resource('donations', DonationController::class);
});

Route::middleware(['auth', 'role:Program Manager'])->prefix('manager')->name('manager.')->group(function () {
    // She works here! Manager approval matrices, school updates, inventory dispatches
});

Route::middleware(['auth', 'role:Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    // School coordinator enrollment posts, shortfall ticket reports, delivery checkmarks
});

require __DIR__.'/auth.php';
