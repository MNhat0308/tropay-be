<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\BillPdfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Endpoint lấy thông tin tài khoản đang đăng nhập
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- CÁC ENDPOINT CHO APP DI ĐỘNG TROPAY MOBILE ---

// Đăng nhập công khai lấy token Sanctum
Route::post('/auth/login', [ApiController::class, 'login']);

// Nhóm các API cần xác thực token Sanctum của di động
Route::middleware('auth:sanctum')->group(function () {
    // API Quản lý phòng trọ
    Route::get('/rooms', [ApiController::class, 'getRooms']);
    Route::post('/rooms', [ApiController::class, 'syncRoom']);

    // API Quản lý khách thuê
    Route::get('/tenants', [ApiController::class, 'getTenants']);
    Route::post('/tenants', [ApiController::class, 'syncTenant']);

    // API Quản lý hóa đơn tính tiền điện nước
    Route::get('/bill-rooms', [ApiController::class, 'getBills']);
    Route::post('/bill-rooms', [ApiController::class, 'syncBill']);
    Route::get('/bill-rooms/{bill}/pdf', BillPdfController::class);
});
