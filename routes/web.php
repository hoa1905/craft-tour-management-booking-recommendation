<?php

use App\Http\Controllers\Client\HomeController;
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
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/load-more-tours', [HomeController::class, 'loadMoreTours'])->name('tours.loadMoreTours');
    Route::get('/tour-detail/{id}', [HomeController::class, 'showTourDetail'])->name('tour.detail');
});
