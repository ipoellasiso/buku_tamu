<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\VisitRequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LaporanController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/guests/scan/{token}', [GuestController::class, 'scanQr']);
Route::get('/visit-requests/scan/{token}', [VisitRequestController::class, 'scanQr']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index']);

    // Visit requests
    Route::get('/visit-requests', [VisitRequestController::class, 'index']);
    Route::post('/visit-requests', [VisitRequestController::class, 'store']);
    Route::get('/visit-requests/token/{token}', [VisitRequestController::class, 'getByToken']); // ← tambah ini
    Route::get('/visit-requests/{visitRequest}', [VisitRequestController::class, 'show']);

    // Admin actions
    Route::middleware('role:admin')->group(function () {
        Route::post('/visit-requests/{visitRequest}/approve', [VisitRequestController::class, 'approve']);
        Route::post('/visit-requests/{visitRequest}/reject', [VisitRequestController::class, 'reject']);
        Route::post('/visit-requests/{visitRequest}/complete', [VisitRequestController::class, 'complete']);

        Route::get('/guests', [GuestController::class, 'index']);
        Route::post('/guests/{guest}/checkout', [GuestController::class, 'checkout']);
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    Route::post('/guests/public', [GuestController::class, 'store']);
    Route::get('/my-visits', [GuestController::class, 'myVisits']);
    Route::post('/guests/{token}/confirm-checkin', [GuestController::class, 'confirmCheckin']);
    Route::post('/guests/{guest}/complete', [GuestController::class, 'complete']);

    Route::middleware('role:admin')->group(function () {
        // ... yang sudah ada
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf']);
        Route::get('/laporan/preview', [LaporanController::class, 'previewData']);
    });
});