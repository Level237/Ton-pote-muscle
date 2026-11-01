<?php

use App\Http\Controllers\Admin\BilanController as AdminBilanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExerciseController as AdminExerciseController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\PlaceholderController as AdminPlaceholderController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\RankingController as AdminRankingController;
use App\Http\Controllers\Admin\WorkoutController as AdminWorkoutController;
use App\Http\Controllers\Auth\LoginAdminController;
use App\Http\Controllers\Auth\LoginMemberController;
use App\Http\Controllers\Auth\LogoutAdminController;
use App\Http\Controllers\Auth\LogoutMemberController;
use App\Http\Controllers\Auth\RegisterMemberController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');
Route::get('programmes/{program}', ProgramController::class)->name('programs.show');

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
    Route::post('exercices/{exercise}/restore', [AdminExerciseController::class, 'restore'])->name('exercises.restore');
    Route::resource('exercices', AdminExerciseController::class)
        ->except('show')
        ->parameters(['exercices' => 'exercise'])
        ->names('exercises');
    Route::post('seances/{workout}/restore', [AdminWorkoutController::class, 'restore'])->name('workouts.restore');
    Route::resource('seances', AdminWorkoutController::class)
        ->except('show')
        ->parameters(['seances' => 'workout'])
        ->names('workouts');
    Route::post('bilans/{bilan}/restore', [AdminBilanController::class, 'restore'])->name('bilans.restore');
    Route::resource('bilans', AdminBilanController::class)->except('show');
    Route::post('programmes/{program}/restore', [AdminProgramController::class, 'restore'])->name('programs.restore');
    Route::resource('programmes', AdminProgramController::class)
        ->except('show')
        ->parameters(['programmes' => 'program'])
        ->names('programs');
    Route::get('inscrits', [AdminMemberController::class, 'index'])->name('enrollments.index');
    Route::get('inscrits/{user}', [AdminMemberController::class, 'show'])->name('enrollments.show');
    Route::get('inscrits/{user}/donner', [AdminMemberController::class, 'give'])->name('enrollments.give');
    Route::post('inscrits/{user}/donner', [AdminMemberController::class, 'storeGive'])->name('enrollments.give.store');
    Route::delete('inscrits/{user}/plan/{item}', [AdminMemberController::class, 'destroyPlanItem'])->name('enrollments.plan.destroy');
    Route::post('inscrits/{user}/deactivate', [AdminMemberController::class, 'deactivate'])->name('enrollments.deactivate');
    Route::post('inscrits/{user}/activate', [AdminMemberController::class, 'activate'])->name('enrollments.activate');
    Route::get('classements', [AdminRankingController::class, 'index'])->name('rankings.index');
    Route::get('classements/{program}', [AdminRankingController::class, 'show'])->name('rankings.show');
    Route::post('classements/{program}/publish', [AdminRankingController::class, 'publish'])->name('rankings.publish');
    Route::get('classements/{program}/export', [AdminRankingController::class, 'export'])->name('rankings.export');
    Route::get('profil', AdminPlaceholderController::class)->name('profile');
    Route::post('logout', LogoutAdminController::class)->name('logout');
});
