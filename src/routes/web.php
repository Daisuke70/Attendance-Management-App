<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminAttendanceCorrectionController;

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

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('show.register');
Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::get('/email/verify', [VerificationController::class, 'showVerificationNotice']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resendVerificationEmail'])->name('verification.resend');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('show.login');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::get('/admin/login', [AdminAuthController::class, 'showAdminLoginForm']);
Route::post('/admin/login', [AdminAuthController::class, 'adminLogin'])->name('admin.login');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/attendance', [AttendanceController::class, 'showAttendanceForm'])->name('attendances.create');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendances.clockIn');
    Route::post('/attendance/break-start', [AttendanceController::class, 'startBreak'])->name('attendances.startBreak');
    Route::post('/attendance/break-end', [AttendanceController::class, 'endBreak'])->name('attendances.endBreak');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendances.clockOut');
    Route::get('/attendance/list', [AttendanceController::class, 'listUserAttendances'])->name('attendances.index');
    Route::get('/attendance/{id}', [AttendanceController::class, 'showAttendanceDetail'])->name('attendances.detail');
    Route::post('/attendance/{id}/corrections', [AttendanceCorrectionController::class, 'submitCorrectionRequest'])
        ->name('attendance.corrections.submit');
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'listUserRequests'])->name('correction-requests.index');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'adminLogout'])->name('admin.logout');
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'listAllAttendances'])->name('admin.attendances.index');
    Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'showStaffAttendance'])->name('admin.attendances.detail');
    Route::get('/admin/staff/list', [AdminStaffController::class, 'listAllStaff'])->name('admin.staffs.index');
    Route::get('/admin/stamp_correction_request/list', [AdminAttendanceCorrectionController::class, 'listAllCorrectionRequests'])->name('admin.correction_requests.index');
});