<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{
    AccountController,
    HomeController,
    SearchController,
    VillageController,
    BookingController,
    MyBookingController,
    PaymentController,
    ChatbotController
};

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

// ==========================================================
// Client routes
// ==========================================================
Route::prefix('')->group(function () {

    // API Chatbot
    Route::post('/api/chat', [ChatbotController::class, 'handleChat'])->name('api.chat');

    // Trang chủ và Tour
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
        Route::get('/load-more-tours', 'loadMoreTours')->name('tours.loadMoreTours');
        Route::get('/tour-detail/{id}', 'showTourDetail')->name('tour.detail');
    });

    Route::get('/tour-search', [SearchController::class, 'search'])->name('tour.search');

    // Làng nghề
    Route::controller(VillageController::class)->name('villages.')->group(function () {
        Route::get('/craft-villages-map', 'map')->name('map');
        Route::get('/village-detail/{id}', 'showVillageDetail')->name('detail');
    });

    // Đăng ký / Đăng nhập
    Route::controller(AccountController::class)
        ->prefix('account')
        ->name('account.')
        ->group(function () {
            Route::post('/register', 'postRegister')->name('register.post');
            Route::post('/login', 'postLogin')->name('login.post');
        });

    // Callbacks/ IPN payment
    Route::controller(PaymentController::class)
        ->prefix('payment')
        ->name('payment.')
        ->group(function () {
            // PayPal Callbacks
            Route::get('/paypal/success', 'paypalSuccess')->name('paypalSuccess');
            Route::get('/paypal/cancel', 'paypalCancel')->name('paypalCancel');

            // VNPAY Callbacks
            Route::get('/vnpay/return', 'vnpayReturn')->name('vnpayReturn'); // User redirect
            Route::get('/vnpay/notify', 'vnpayNotify')->name('vnpayNotify'); // Server IPN
        });


    // Các route yêu cầu đăng nhập (auth:client)
    Route::middleware('auth:client')->group(function () {

        // Tài khoản cá nhân
        Route::controller(AccountController::class)
            ->prefix('account')
            ->name('account.')
            ->group(function () {
                Route::get('/info', 'showAccountInfo')->name('info');
                Route::post('/update-info', 'updateAccountInfo')->name('updateInfo');
                Route::post('/update-avatar', 'updateAvatar')->name('updateAvatar');
                Route::get('/change-password', 'showChangePasswordForm')->name('changePassword');
                Route::post('/change-password', 'updatePassword')->name('passwordUpdate');
                Route::post('/logout', 'logout')->name('logout');
            });

        // Quản lý đơn đặt tour
        Route::controller(MyBookingController::class)
            ->prefix('account/my-bookings')
            ->name('account.myBookings')
            ->group(function () {
                Route::get('/', 'index')->name(''); // Không có hậu tố
                Route::get('/detail/{booking}', 'show')->name('.detail');
                Route::post('/cancel/{booking}', 'cancel')->name('.cancel');
            });

        // Đặt tour
        Route::controller(BookingController::class)->group(function () {
            Route::post('/booking/create', 'create')->name('booking.create');
            Route::get('/booking', 'checkout')->name('booking');
        });

        // Thanh toán
        Route::controller(PaymentController::class)
            ->prefix('payment')
            ->name('payment.')
            ->group(function () {
                Route::post('/office', 'officePayment')->name('officePayment');
                Route::post('/paypal', 'paypal')->name('paypal');
                Route::post('/vnpay', 'vnpayPayment')->name('vnpay');
            });
    });
});
