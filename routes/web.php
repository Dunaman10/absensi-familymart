<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\GeofenceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password Routes (AJAX)
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
use App\Http\Controllers\Admin\ShiftController;

// Route Admin Workspace (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Management & Detail Tracking (Payroll)
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Shift Management
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{id}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    Route::post('/shifts/assign', [ShiftController::class, 'assignEmployee'])->name('shifts.assign');

    // Attendance Logs & Reason Approvals
    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])->name('attendance_logs.index');
    Route::get('/attendance-logs/export', [AttendanceLogController::class, 'export'])->name('attendance_logs.export');
    Route::post('/attendance-logs/{id}/approve', [AttendanceLogController::class, 'approveReason'])->name('attendance_logs.approve');
    Route::post('/attendance-logs/{id}/reject', [AttendanceLogController::class, 'rejectReason'])->name('attendance_logs.reject');

    // Geofences & Settings
    Route::get('/geofences', [GeofenceController::class, 'index'])->name('geofences.index');
    Route::post('/geofences', [GeofenceController::class, 'store'])->name('geofences.store');
    Route::put('/geofences/{id}', [GeofenceController::class, 'update'])->name('geofences.update');
    Route::delete('/geofences/{id}', [GeofenceController::class, 'destroy'])->name('geofences.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Route Employee Workspace (Protected)
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role.employee'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance/scan', [EmployeeAttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/store', [EmployeeAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/history', [EmployeeAttendanceController::class, 'history'])->name('history');
    Route::get('/profile', [App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Employee\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Alias /dashboard redirect based on authenticated user role
Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('employee.dashboard');
})->middleware('auth');
