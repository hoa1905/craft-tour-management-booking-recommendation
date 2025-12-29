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
  ChatbotController,
  RecommendationController,
  ReviewController,
  TourRequestController
};
use App\Http\Controllers\Admin\{
  AdminAccountController,
  AdminBookingController,
  AdminReviewController,
  AdminTourRequestController,
  ClientController,
  DashboardController,
  AdminVillageController,
  TourController,
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

    // Tour theo yêu cầu
    Route::controller(TourRequestController::class)
      ->prefix('tour-request')
      ->name('tour_request.')
      ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('list/', 'index')->name('index');
        Route::get('/{id}', 'detail')->name('detail');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::post('/{id}/cancel', 'cancel')->name('cancel');
        Route::get('/{id}/checkout', 'checkout')->name('checkout');
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

    // Đánh giá
    Route::controller(ReviewController::class)
      ->prefix('review')
      ->group(function () {
        Route::get('/list/{tour_id}', 'loadReviews');
        Route::post('/create', 'store');
      });
  });
});

// ==========================================================
// Admin routes
// ==========================================================
Route::prefix('admin')->name('admin.')->group(function () {
  // Các route không yêu cầu đăng nhập
  Route::controller(AdminAccountController::class)
    ->group(function () {
      Route::get('login', 'showLoginForm')->name('login');
      Route::post('login', 'login')->name('login.post');
    });
  // Route::get('/dashboard', function () {
  //     return view('admin.dashboard');
  // })->name('dashboard');

  // Các route yêu cầu đăng nhập
  Route::middleware('auth:admin')->group(function () {

    // Dashboard và API thống kê cung cấp dữ liệu cho Chart.js
    Route::controller(DashboardController::class)
      ->prefix('dashboard')
      ->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('data/monthly-revenue', 'getMonthlyRevenue');
        Route::get('data/weekly-revenue', 'getWeeklyRevenue');
        Route::get('data/daily-revenue', 'getDailyRevenue');
        Route::get('data/order-status', 'getOrderStatus');
        Route::get('data/top-tours', 'getTopTours');
        Route::get('data/top-booked-tours', 'getTopBookedTours');
        Route::get('data/payment-methods', 'getPaymentMethods');
      });

    // Quản lý tài khoản admin
    Route::controller(AdminAccountController::class)
      ->prefix('profile')
      ->name('profile.')
      ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::put('update-info', 'updateAccountInfo')->name('updateInfo');
        Route::put('update-avatar', 'updateAvatar')->name('updateAvatar');
        Route::delete('delete-avatar', 'deleteAvatar')->name('deleteAvatar');
        Route::put('updatePassword', 'updatePassword')->name('updatePassword');
        Route::post('logout', 'logout')->name('logout');
      });

    // Quản lý khách hàng
    Route::controller(ClientController::class)
      ->prefix('client')
      ->name('client.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('edit/{id}', 'showEditForm')->name('showEdit');
        Route::put('update/{id}', 'updateAccountInfo')->name('update');
        Route::get('add-account', 'showAddAccountForm')->name('showAddAccount');
        Route::post('add', 'addAccountForm')->name('addAccount');
        Route::put('toggleLock/{id}', 'toggleLock')->name('toggleLock');
        Route::delete('delete/{id}', 'deleteAccount')->name('delete');
      });

    // Quản lý làng nghề
    Route::controller(AdminVillageController::class)
      ->prefix('village')
      ->name('village.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('village-detail/{id}', 'villageDetail')->name('detail');
        Route::get('show-edit-form/{id}', 'showEditForm')->name('showEditForm');
        Route::put('update/{id}', 'updateVillage')->name('update');
        Route::get('add', 'showAddVillageForm')->name('showAddVillage');
        Route::post('add', 'addVillage')->name('addVillage');
        Route::put('toggle-status/{id}', 'toggleStatus')->name('toggleStatus');
        Route::delete('delete/{id}', 'deleteVillage')->name('delete');
      });

    // Quản lý tour
    Route::controller(TourController::class)
      ->prefix('tour')
      ->name('tour.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('tour-detail/{id}', 'tourDetail')->name('detail');
        Route::get('show-edit-form/{id}', 'showEditForm')->name('showEditForm');
        Route::put('update/{id}', 'updateTour')->name('update');
        Route::get('add', 'showAddTourForm')->name('showAddTour');
        Route::post('add', 'addTour')->name('addTour');
        Route::put('toggle-status/{id}', 'toggleStatus')->name('toggleStatus');
        Route::delete('delete/{id}', 'deleteTour')->name('delete');
      });

    // Quản lý đơn đặt tour
    Route::controller(AdminBookingController::class)
      ->prefix('booking')
      ->name('booking.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::put('update-status/{id}', 'updateStatus')->name('updateStatus');
        Route::put('update-payment-status/{id}', 'updatePaymentStatus')->name('updatePaymentStatus');
        Route::get('{id}/invoice', 'invoice')->name('invoice');
      });

    // Quản lý tour theo yêu cầu
    Route::controller(AdminTourRequestController::class)
      ->prefix('tour-request')
      ->name('tour_request.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::get('/detail/{id}', 'detail')->name('detail');
        Route::put('/update/{id}', 'update')->name('update');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/edit/{id}', 'updateContent')->name('update_content');
      });

    // Đánh giá
    Route::controller(AdminReviewController::class)
      ->prefix('review')
      ->name('review.')
      ->group(function () {
        Route::get('list', 'index')->name('index');
        Route::delete('/delete/{id}', 'delete')->name('delete');
      });
  });
});


// ===== RECOMMENDATION ROUTES =====
Route::controller(RecommendationController::class)
  ->prefix('api/recommendations')
  ->name('recommendations.')
  ->group(function () {
    // Gợi ý cho trang chủ (hỗ trợ cả guest và auth)
    Route::get('/home', 'homeRecommendations')
      ->name('home');

    // Gợi ý tour tương tự (trang chi tiết tour)
    Route::get('/similar/{tourId}', 'similarTours')
      ->name('similar');

    // Gợi ý dựa trên từ khóa tìm kiếm
    Route::get('/search', 'searchRecommendations')
      ->name('search');

    // Track tour đã xem (lưu cookie)
    Route::post('/track-view', 'trackTourView')
      ->name('trackView');
  });
