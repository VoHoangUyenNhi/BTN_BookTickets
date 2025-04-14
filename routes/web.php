<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function ()- {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
*/
//Tạo cập nhật thông tin người dùng
Route::get('/accountpanel','App\Http\Controllers\AccountController@accountpanel')
->middleware('auth')->name("account");

Route::post('/saveaccountinfo','App\Http\Controllers\AccountController@saveaccountinfo')
->middleware('auth')->name('saveinfo');

//TRANG CHỦ INDEX
Route::get('/', 'App\Http\Controllers\BookTicketController@index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Thu Thủy
Route::get('/search-ticket', [App\Http\Controllers\BookTicketController::class, 'searchTrips'])->name('search_ticket');


require __DIR__.'/auth.php';

// Anh Thu
// --- Quy trình Đặt Vé (Do BookingController xử lý) ---
Route::prefix('dat-ve')->name('dat-ve.')->group(function () {
    // Bước 1: Hiển thị trang chọn ghế (GET)
    Route::get('/chon-ghe/{maChuyenDi}', [BookingController::class, 'showSeatSelection'])->name('chon-ghe');

    // Bước 2: Lưu ghế đã chọn & chuyển hướng (POST)
    Route::post('/luu-ghe', [BookingController::class, 'storeSelectedSeats'])->name('luu-ghe');

    // Bước 3: Hiển thị form nhập thông tin (GET)
    Route::get('/chi-tiet', [BookingController::class, 'showCustomerDetailsForm'])->name('chi-tiet');

    // Bước 4: Xử lý đặt vé, lưu DB & chuyển hướng (POST)
    Route::post('/xac-nhan', [BookingController::class, 'processBooking'])->name('xac-nhan');

    // Bước 5: Hiển thị trang thành công (GET)
    Route::get('/thanh-cong', [BookingController::class, 'showSuccessPage'])->name('thanh-cong');
});

