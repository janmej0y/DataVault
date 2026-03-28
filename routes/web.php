<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DuplicateController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IncompleteRecordController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
    Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');

    Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('/businesses/export', [BusinessController::class, 'export'])->name('businesses.export');
    Route::delete('/businesses/bulk-delete', [BusinessController::class, 'bulkDelete'])->name('businesses.bulk-delete');

    Route::get('/duplicates', [DuplicateController::class, 'index'])->name('duplicates.index');
    Route::get('/duplicates/export', [DuplicateController::class, 'export'])->name('duplicates.export');

    Route::get('/merge-records', [MergeController::class, 'index'])->name('merges.index');
    Route::post('/merge-records', [MergeController::class, 'store'])->name('merges.store');

    Route::get('/incomplete-records', [IncompleteRecordController::class, 'index'])->name('incomplete.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
