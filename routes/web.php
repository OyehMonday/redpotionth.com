<?php

use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminSignupController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameCategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameTopupController;


require __DIR__.'/auth.php';

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/games/{id}/topup', [GameTopupController::class, 'show'])->name('games.topup');

Route::get('/auth/google', [CustomAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [CustomAuthController::class, 'handleGoogleCallback']);

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

Route::get('/signup', [CustomAuthController::class, 'showSignUpForm'])->name('custom.signup.form');
Route::post('/signup', [CustomAuthController::class, 'signUp'])->name('custom.signup');

Route::get('/login', [CustomAuthController::class, 'showLoginForm'])->name('custom.login.form');
Route::post('/login', [CustomAuthController::class, 'login'])->name('custom.login');

Route::get('/dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');
Route::get('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Route::prefix('admin')->group(function () {
//     // Admin login route
//     Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
//     Route::post('/login', [AdminAuthController::class, 'login']);
    
//     // Admin signup route (if you want a signup form)
//     Route::get('/signup', [AdminAuthController::class, 'showSignupForm'])->name('admin.signup');
//     Route::post('/signup', [AdminAuthController::class, 'signup']);

//     Route::middleware('auth:admin')->group(function () {
//         Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
//         Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
//     });
// });

// Route::get('/admin/verify/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');

// Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/admin/login', [AdminAuthController::class, 'login']);
// Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

// Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
//     Route::resource('game-categories', \App\Http\Controllers\Admin\GameCategoryController::class);
// });

// Route::prefix('admin')->middleware('auth:admin')->group(function () {
//     Route::resource('game-categories', \App\Http\Controllers\Admin\GameCategoryController::class);
//     Route::resource('games', \App\Http\Controllers\Admin\GameController::class);
// });

Route::prefix('admin')->group(function () {
    // Admin authentication routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/signup', [AdminAuthController::class, 'showSignupForm'])->name('admin.signup');
    Route::post('/signup', [AdminAuthController::class, 'signup']);
    Route::get('/verify/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // Game Management Routes
        Route::resource('game-categories', GameCategoryController::class);
        Route::resource('games', GameController::class);
        
        // Sorting Route for Drag & Drop Games
        Route::post('/games/sort', [GameController::class, 'sort'])->name('games.sort');        
    });
});
