<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Admin\ContentController; // ✅ Add this line

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // License Management
    Route::get('/licenses', [LicenseController::class, 'index'])->name('licenses.index');

    // Website Management
    Route::get('/websites', [WebsiteController::class, 'index'])->name('websites.index');

    // ✅ Article / Content Management
    Route::resource('articles', ContentController::class)->names('articles');
});
