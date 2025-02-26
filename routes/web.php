<?php

use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AllGameController;
use App\Http\Controllers\GameTopupController;
use App\Http\Controllers\GameCartController;
use App\Http\Controllers\FacebookCommentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminOrderController;

use App\Http\Controllers\Admin\GamePackageController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameCategoryController;

use App\Http\Controllers\Auth\AdminSignupController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\LineNotificationService;
use App\Models\Order;
use App\Models\Admin;

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

Route::get('/load-more-orders', function (Request $request) {
    if (!Session::has('user')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $sessionUser = Session::get('user');
    $user = \App\Models\User::find($sessionUser->id);

    $offset = $request->input('offset', 0);

    $orders = Order::where('user_id', $user->id)
        ->latest()
        ->offset($offset)
        ->limit(5)
        ->get()
        ->map(function ($order) {
            $cartDetails = json_decode($order->cart_details, true) ?? [];

            $restructuredCart = [];
            foreach ($cartDetails as $gameId => $game) {
                $gameModel = \App\Models\Game::find($gameId);
                
                $restructuredCart[] = [
                    'game_id' => $gameId,
                    'game_name' => $game['game_name'] ?? ($gameModel->name ?? 'ไม่ระบุ'),
                    'cover_image' => str_replace('\\', '/', $game['cover_image'] ?? ($gameModel->cover_image ?? 'default.jpg')),
                    'player_id' => $game['player_id'] ?? 'ไม่ระบุ',
                    'packages' => array_values($game['packages'] ?? []), 
                ];
            }

            return [
                'id' => $order->id,
                'created_at' => $order->created_at->toDateTimeString(),
                'status' => $order->status,
                'used_coins' => $order->used_coins ?? 0,
                'coin_earned' => $order->coin_earned ?? 0,
                'total_price' => $order->total_price,
                'cart_details' => $restructuredCart, 
            ];
        });

    return response()->json($orders);
});


Route::get('/admin/verify/{token}', [AdminAuthController::class, 'verify'])->name('admin.verify');
Route::get('/verify-email/{token}', [CustomAuthController::class, 'verifyEmail'])->name('verify.email');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/games', [AllGameController::class, 'index'])->name('games.all');

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
Route::get('/admin/users/{userId}/orders', [AdminOrderController::class, 'showUserOrders'])->name('admin.user.orders');

Route::get('/fetch-facebook-comments', [FacebookCommentController::class, 'fetchComments'])->name('fetch.facebook.comments');

Route::get('/admin/orders/new', [AdminOrderController::class, 'getNewOrders']);

Route::post('/admin/orders/{order}/mark-in-process', [AdminOrderController::class, 'markInProcess']);
Route::post('/admin/orders/{order}/markCompleted', [AdminOrderController::class, 'markCompleted']);
Route::post('/admin/orders/{order}/cancel', [AdminOrderController::class, 'cancelOrder']);
Route::get('/admin/orders/unfinished', [AdminOrderController::class, 'showUnfinishedOrders'])->name('admin.orders.unfinished');
Route::get('/admin/orders/{order}/details', [AdminOrderController::class, 'showOrderDetails'])->name('admin.orders.details');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/return-policy', function () {
    return view('return-policy');
});

Route::get('/search', [AllGameController::class, 'search'])->name('games.search');
