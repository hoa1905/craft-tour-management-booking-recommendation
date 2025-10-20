<?php

use App\Http\Controllers\Client\AccountController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Client routes
Route::group(['prefix' => ''], function () {
    // Trang chính và tour
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/load-more-tours', [HomeController::class, 'loadMoreTours'])->name('tours.loadMoreTours');
    Route::get('/tour-detail/{id}', [HomeController::class, 'showTourDetail'])->name('tour.detail');
    Route::get('/tour-search', [SearchController::class, 'search'])->name('tour.search');

    // Đăng ký và đăng nhập
    Route::group(['prefix' => 'account'], function () {
        Route::post('/register', [AccountController::class, 'postRegister'])->name('account.register.post');
        Route::post('/login', [AccountController::class, 'postLogin'])->name('account.login.post');
    });

    // Nhóm cần đăng nhập (middleware auth:client)
    Route::middleware('auth:client')->prefix('account')->group(function () {
        Route::get('/info', [AccountController::class, 'info'])->name('account.info');
        Route::get('/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');
        Route::post('/logout', [AccountController::class, 'logout'])->name('account.logout');

        // Đặt tour và xem đơn
        Route::get('/booking', [AccountController::class, 'bookings'])->name('account.bookings');
        Route::get('/account/bookings', [AccountController::class, 'listBookings'])->name('account.bookings');
        Route::post('/booking/create', [AccountController::class, 'create'])->name('booking.create');
    });
});
