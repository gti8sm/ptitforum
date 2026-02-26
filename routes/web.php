<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvitationAcceptanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\GroupPlanningController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('home');
});

Route::middleware('guest')->group(function () {
    Route::get('/invitations/{token}', [InvitationAcceptanceController::class, 'show'])
        ->name('invitations.show');
    Route::post('/invitations/{token}', [InvitationAcceptanceController::class, 'accept'])
        ->name('invitations.accept');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');

    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::get('/groups/{group}/planning', [GroupPlanningController::class, 'index'])->name('planning.index');
    Route::get('/groups/{group}/planning/create', [GroupPlanningController::class, 'create'])->name('planning.create');
    Route::post('/groups/{group}/planning', [GroupPlanningController::class, 'store'])->name('planning.store');
    Route::get('/groups/{group}/planning/{event}', [GroupPlanningController::class, 'show'])->name('planning.show');
    Route::get('/groups/{group}/planning/{event}/edit', [GroupPlanningController::class, 'edit'])->name('planning.edit');
    Route::put('/groups/{group}/planning/{event}', [GroupPlanningController::class, 'update'])->name('planning.update');
    Route::delete('/groups/{group}/planning/{event}', [GroupPlanningController::class, 'destroy'])->name('planning.destroy');
    Route::post('/groups/{group}/planning/{event}/rsvp', [GroupPlanningController::class, 'rsvp'])->name('planning.rsvp');
    Route::post('/groups/{group}/planning/{event}/tasks', [GroupPlanningController::class, 'storeTask'])->name('planning.tasks.store');
    Route::put('/groups/{group}/planning/{event}/tasks/{task}', [GroupPlanningController::class, 'updateTaskStatus'])->name('planning.tasks.update-status');
    Route::delete('/groups/{group}/planning/{event}/tasks/{task}', [GroupPlanningController::class, 'destroyTask'])->name('planning.tasks.destroy');

    Route::get('/groups/{group}/{thread}', [ThreadController::class, 'show'])->name('threads.show');
    Route::post('/groups/{group}/{thread}/posts', [ThreadController::class, 'storePost'])->name('threads.posts.store');
    Route::post('/groups/{group}/{thread}/toggle-pin', [ThreadController::class, 'togglePin'])->name('threads.toggle-pin');
    Route::post('/groups/{group}/{thread}/toggle-lock', [ThreadController::class, 'toggleLock'])->name('threads.toggle-lock');
    Route::delete('/groups/{group}/{thread}/posts/{post}', [ThreadController::class, 'destroyPost'])->name('threads.posts.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
