<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\LogbookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => $request->user(),
            ]);
        });

        Route::get('/attendances', [AttendanceController::class, 'index']);
        Route::post('/attendances', [AttendanceController::class, 'store']);

        Route::get('/logbooks', [LogbookController::class, 'index']);
        Route::post('/logbooks', [LogbookController::class, 'store']);

        Route::get('/leaves', [LeaveController::class, 'index']);
        Route::post('/leaves', [LeaveController::class, 'store']);
        Route::post('/leaves/{id}/verify', [LeaveController::class, 'verify']);
    });
});
