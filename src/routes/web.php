<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;

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

Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify', [VerificationController::class, 'showVerificationNotice']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resendVerificationEmail'])->name('verification.resend');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/attendance', [AttendanceController::class, 'showAttendanceForm'])->name('attendances.create');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/break-start', [AttendanceController::class, 'startBreak'])->name('attendance.startBreak');
    Route::post('/attendance/break-end', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::get('/attendance/list', [AttendanceController::class, 'listUserAttendances'])->name('attendances.index');
    Route::get('/attendance/{id}', [AttendanceController::class, 'showAttendanceDetail'])->name('attendances.detail');
    Route::post('/attendance/{id}/corrections', [AttendanceCorrectionController::class, 'submitCorrectionRequest'])
        ->name('attendance.corrections.submit');
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'listUserRequests'])->name('correction-requests.index');
});