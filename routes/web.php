<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicDonationController;
use Illuminate\Support\Facades\Route;

// 1. Open Public Routes
Route::get('/', function () { return view('welcome'); });
Route::post('/pledge', [PublicDonationController::class, 'store'])->name('public.pledge');

// 2. Base Centralized Shared Dashboard Access Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Isolated Private Role Routing Guard Enclaves
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // You work here! Place all your specific Admin management views/routes inside this group
    // Route::get('/managers', [AdminManagerController::class, 'index'])->name('managers.index');
});

Route::middleware(['auth', 'role:Program Manager'])->prefix('manager')->name('manager.')->group(function () {
    // She works here! Manager approval matrices, school updates, inventory dispatches
});

Route::middleware(['auth', 'role:Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    // School coordinator enrollment posts, shortfall ticket reports, delivery checkmarks
});

require __DIR__.'/auth.php';

