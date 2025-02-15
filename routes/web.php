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
use App\Http\Controllers\GameCartController;
use App\Http\Controllers\Admin\GamePackageController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FacebookCommentController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\LineNotificationService;
use App\Http\Controllers\AdminOrderController;
use App\Models\Order;

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $allowedFolders = ['game_covers', 'game_full_covers', 'package_covers', 'uidimages', 'payments']; 

    if (!in_array($folder, $allowedFolders)) {
        abort(403, 'Unauthorized access');
    }

    $path = storage_path("app/public/$folder/$filename");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

require __DIR__.'/auth.php';

Route::get('/admin/verify/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');
Route::get('/verify-email/{token}', [CustomAuthController::class, 'verifyEmail'])->name('verify.email');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/games/{id}/topup', [GameTopupController::class, 'show'])->name('games.topup');
Route::post('/games/cart/add', [GameCartController::class, 'addToCart'])->name('game.cart.add');

Route::post('/games/cart/add', [GameCartController::class, 'addToCart'])->name('game.cart.add');
Route::get('/games/cart', [GameCartController::class, 'viewCart'])->name('game.cart.view');
Route::post('/games/cart/update', [GameCartController::class, 'updateCart'])->name('game.cart.update');
Route::get('/games/cart/remove', [GameCartController::class, 'removeFromCart'])->name('game.cart.remove');
Route::post('/games/cart/clear', [GameCartController::class, 'clearCart'])->name('game.cart.clear');

Route::get('/games/checkout', [GameCartController::class, 'checkout'])->name('game.checkout');
Route::get('/game/checkout/{order_id}', [GameCartController::class, 'showCheckout'])->name('game.checkout.view');
Route::post('/game/payment/confirm/{order_id}', [GameCartController::class, 'confirmPayment'])->name('game.payment.confirm');

Route::get('/payment/qr/{receiver}/{amount}', [PaymentController::class, 'generatePromptPayQR']);

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
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/signup', [AdminAuthController::class, 'showSignupForm'])->name('admin.signup');
    Route::post('/signup', [AdminAuthController::class, 'signup']);
    Route::get('/verify/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::resource('game-categories', GameCategoryController::class);
        Route::resource('games', GameController::class);
        Route::post('/games/sort', [GameController::class, 'sort'])->name('games.sort');        
    });
});

Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::resource('games', GameController::class);
    Route::get('/games/{game}/packages', [GamePackageController::class, 'index'])->name('game-packages.index');
    Route::get('/games/{game}/packages/create', [GamePackageController::class, 'create'])->name('game-packages.create');
    Route::post('/games/{game}/packages', [GamePackageController::class, 'store'])->name('game-packages.store');
    Route::get('/games/{game}/packages/{package}/edit', [GamePackageController::class, 'edit'])->name('game-packages.edit');
    Route::put('/games/{game}/packages/{package}', [GamePackageController::class, 'update'])->name('game-packages.update');
    Route::delete('/games/{game}/packages/{package}', [GamePackageController::class, 'destroy'])->name('game-packages.destroy');
    Route::post('/games/{game}/packages/sort', [GamePackageController::class, 'sort'])->name('game-packages.sort');
    
});

Route::get('admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
Route::post('admin/orders/approve/{orderId}', [AdminOrderController::class, 'approvePayment'])->name('admin.orders.approve');
Route::post('/admin/orders/{order}/mark-in-process', [AdminOrderController::class, 'markInProcess'])->name('admin.orders.markInProcess');
Route::post('/admin/orders/{order}/markCompleted', [AdminOrderController::class, 'markCompleted'])->name('admin.orders.markCompleted');



Route::get('/fetch-facebook-comments', [FacebookCommentController::class, 'fetchComments'])->name('fetch.facebook.comments');

Route::get('/admin/orders/new', [AdminOrderController::class, 'getNewOrders']);


Route::get('/admin/test-orders', function () {
    $orders = Order::orderBy('created_at', 'desc')->limit(5)->get();
    return view('admin.orders.test-orders', compact('orders'));
});
