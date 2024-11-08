<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;

// Halaman Utama
Route::get('/', [Controller::class, 'index'])->name('Home');



// CART
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');


// DATA
Route::resource('products', ProductController::class);



// LOGIN
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// REGISTER
Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Rute dashboard untuk admin dan pengguna biasa berdasarkan role
Route::middleware(['auth'])->group(function () {
    // Rute untuk admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // Halaman dashboard admin
    })->middleware('can:isAdmin')->name('admin');

    // Rute untuk user yang diarahkan ke halaman Home
    Route::get('/user/dashboard', function () {
        return redirect()->route('Home'); // Mengarahkan ke halaman Home
    })->middleware('can:isUser')->name('user.dashboard');
});


// PAYMENT
Route::middleware(['auth'])->group(function () {
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
    Route::post('/payment/upload', [PaymentController::class, 'uploadPaymentProof'])->name('payment.upload');
});


// TRANSACTION View
Route::middleware(['auth'])->group(function () {
    // Route untuk menampilkan transaksi pengguna
    Route::get('/user/transactions', [UserController::class, 'showTransactions'])->name('transactions');
});



// ONGKIR
Route::get('/province', [PaymentController::class, 'get_province'])->name('province');
