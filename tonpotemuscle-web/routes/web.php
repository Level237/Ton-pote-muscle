<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PlaceholderController as AdminPlaceholderController;
use App\Http\Controllers\Auth\LoginAdminController;
use App\Http\Controllers\Auth\LoginMemberController;
use App\Http\Controllers\Auth\LogoutAdminController;
use App\Http\Controllers\Auth\LogoutMemberController;
use App\Http\Controllers\Auth\RegisterMemberController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Member\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('register', [RegisterMemberController::class, 'create'])->name('register');
    Route::post('register', [RegisterMemberController::class, 'store'])->middleware('throttle:auth');

    Route::get('login', [LoginMemberController::class, 'create'])->name('login');
    Route::post('login', [LoginMemberController::class, 'store'])->middleware('throttle:auth');

    Route::get('admin/login', [LoginAdminController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [LoginAdminController::class, 'store'])->middleware('throttle:auth');
});

Route::middleware(['auth', 'member'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('logout', LogoutMemberController::class)->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('exercices', AdminPlaceholderController::class)->name('exercises.index');
    Route::get('seances', AdminPlaceholderController::class)->name('workouts.index');
    Route::get('programmes', AdminPlaceholderController::class)->name('programs.index');
    Route::get('inscrits', AdminPlaceholderController::class)->name('enrollments.index');
    Route::get('classements', AdminPlaceholderController::class)->name('rankings.index');
    Route::post('logout', LogoutAdminController::class)->name('logout');
});
