<?php

use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminSignupController;

Route::get('/admin/verify/{token}', [AdminSignupController::class, 'verify'])->name('admin.verify');

Route::get('/', function () {
    return view('landing');
});

Route::get('/landing', function () {
    return view('landing');
})->name('landing.page');

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

Route::prefix('admin')->group(function () {
    // Admin login route
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // Admin signup route (if you want a signup form)
    Route::get('/signup', [AdminAuthController::class, 'showSignupForm'])->name('admin.signup');
    Route::post('/signup', [AdminAuthController::class, 'signup']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

Route::get('/admin/verify/{id}/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');