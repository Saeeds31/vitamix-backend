<?php

use Illuminate\Support\Facades\Route;
use Modules\Major\Http\Controllers\MajorController;

Route::middleware(['auth:sanctum'])->prefix('v1/admin')->group(function () {
    Route::get('/requests', [MajorController::class, 'index'])->name('major.index');
    Route::get('/requests/{id}', [MajorController::class, 'show'])->name('major.show');
    Route::put('/requests/{id}/status', [MajorController::class, 'updateStatus'])->name('major.update-status');
    Route::put('/requests/{id}/summary', [MajorController::class, 'updateLastCallSummary'])->name('major.update-summary');
    Route::delete('/requests/{id}', [MajorController::class, 'destroy'])->name('major.destroy');
    Route::get('/statistics', [MajorController::class, 'statistics'])->name('major.statistics');
});
Route::post('v1/front/requests', [MajorController::class, 'store'])->name('major.store');
