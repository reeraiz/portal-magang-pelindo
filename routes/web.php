<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (in_array(auth()->user()->role, ['admin', 'pembimbing'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('intern.absensi');
    })->name('dashboard');

    // Intern Routes
    Route::middleware(['role:intern'])->prefix('intern')->name('intern.')->group(function () {
        Route::get('/absensi', [InternController::class, 'absensi'])->name('absensi');
        Route::post('/absensi', [InternController::class, 'storeAbsensi'])->name('absensi.store')->middleware('throttle:10,1');
        Route::post('/absensi/izin', [InternController::class, 'storeIzin'])->name('absensi.izin')->middleware('throttle:5,1');
        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves');
        Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store')->middleware('throttle:10,1');
        Route::get('/logbook', [InternController::class, 'logbook'])->name('logbook');
        Route::post('/logbook', [InternController::class, 'storeLogbook'])->name('logbook.store')->middleware('throttle:10,1');
        Route::put('/logbook/{id}', [InternController::class, 'updateLogbook'])->name('logbook.update')->middleware('throttle:10,1');
        Route::delete('/logbook/{id}', [InternController::class, 'destroyLogbook'])->name('logbook.destroy')->middleware('throttle:10,1');
        Route::get('/logbook/print', [InternController::class, 'printLogbook'])->name('logbook.print');
        Route::get('/rekap', [InternController::class, 'rekap'])->name('rekap');
        Route::get('/absensi/print', [InternController::class, 'printAbsensi'])->name('absensi.print');
    });

    // Admin & Pembimbing Routes
    Route::middleware(['role:admin,pembimbing'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/notifications/trigger', [AdminController::class, 'triggerNotificationGateway'])->name('notifications.trigger');
        Route::get('/absensi', [AdminController::class, 'absensi'])->name('absensi');
        Route::get('/absensi/export', [AdminController::class, 'exportAbsensi'])->name('absensi.export');
        Route::get('/absensi/print', [AdminController::class, 'printAbsensi'])->name('absensi.print');
        Route::get('/leaves', [LeaveController::class, 'adminIndex'])->name('leaves');
        Route::get('/logbook', [AdminController::class, 'logbook'])->name('logbook');
        Route::get('/logbook/export', [AdminController::class, 'exportLogbook'])->name('logbook.export');
        Route::get('/logbook/print', [AdminController::class, 'printLogbook'])->name('logbook.print');
        Route::get('/interns', [AdminController::class, 'interns'])->name('interns');
        Route::get('/interns/export/excel', [ExportController::class, 'exportExcel'])->name('interns.export.excel');
        Route::get('/interns/export/pdf', [ExportController::class, 'exportPdf'])->name('interns.export.pdf');
        Route::get('/interns/{id}/cv', [AdminController::class, 'generateCv'])->name('interns.cv');
        Route::post('/interns/certificate/send', [AdminController::class, 'sendCertificate'])->name('certificate.send');
        Route::post('/interns/certificate/review-skripsi', [AdminController::class, 'reviewSkripsi'])->name('certificate.review-skripsi');
        
        // Shift Management
        Route::post('/interns/bulk-shift', [AdminController::class, 'bulkUpdateShift'])->name('interns.bulk-shift');
        Route::post('/verify-absensi/{id}', [AdminController::class, 'verifyAbsensi'])->name('verify.absensi')->middleware('throttle:30,1');
        Route::post('/verify-leave/{id}', [LeaveController::class, 'verify'])->name('verify.leave')->middleware('throttle:30,1');
        Route::post('/verify-logbook/{id}', [AdminController::class, 'verifyLogbook'])->name('verify.logbook')->middleware('throttle:30,1');
        Route::post('/verify-logbook-bulk', [AdminController::class, 'bulkVerifyLogbook'])->name('verify.logbook.bulk')->middleware('throttle:30,1');
        Route::put('/interns/{id}', [AdminController::class, 'updateIntern'])->name('interns.update')->middleware('throttle:20,1');
        Route::put('/mentors/{id}', [AdminController::class, 'updateMentor'])->name('mentors.update')->middleware('throttle:20,1');
        Route::post('/interns/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('interns.reset-password')->middleware('throttle:10,1');
        Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password')->middleware('throttle:10,1');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store')->middleware('throttle:10,1');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy')->middleware('throttle:10,1');
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/master-data', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data');
        Route::post('/master-data/{type}', [\App\Http\Controllers\MasterDataController::class, 'store'])->name('master-data.store');
        Route::delete('/master-data/{type}/{id}', [\App\Http\Controllers\MasterDataController::class, 'destroy'])->name('master-data.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/skripsi', [ProfileController::class, 'uploadSkripsi'])->name('profile.skripsi.upload');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
