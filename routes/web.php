<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookTicketController;
use App\Http\Controllers\AccountController;


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
Route::get('/', function () {
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
Route::get('/', [BookTicketController::class, 'index'])->name('home');

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
// Trong routes/web.php

// Anh Thu
Route::get('/chon_ghe/{maChuyenDi}', [BookTicketController::class, 'showSeatSelection'])
    ->where('maChuyenDi', '[0-9]+') // Đảm bảo maChuyenDi là số
    ->name('bookticket.seat_selection');
// ROUTE NÀY: Xử lý POST từ trang chọn ghế ***
Route::post('/xu-ly-chon-ghe', [BookTicketController::class, 'processSeatSelection'])
    ->name('bookticket.process_booking'); // Giữ nguyên name từ action của form

// ROUTE NÀY: Hiển thị trang xác nhận đặt vé ***
Route::get('/xac-nhan-dat-ve', [BookTicketController::class, 'showConfirmationPage'])
    ->name('bookticket.show_confirmation');
    Route::post('/hoan-tat-dat-ve', [BookTicketController::class, 'finalizeBooking'])
    ->name('bookticket.finalize'); // Đặt tên khớp với view
// *** THÊM ROUTE NÀY: Hiển thị trang đặt vé thành công ***
Route::get('/dat-ve-thanh-cong', [BookTicketController::class, 'showSuccessPage'])
    ->name('bookticket.success');
   


require __DIR__.'/auth.php';
