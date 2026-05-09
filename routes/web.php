<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\RequestInvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect('/admin')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});


Route::middleware('throttle:invoice-request')->group(function () {
    Route::get('/request-invoice', [RequestInvoiceController::class, 'create'])->name('request-invoice.create');
    Route::post('/request-invoice', [RequestInvoiceController::class, 'store'])->name('request-invoice.store');
});
Route::get('/request-invoice/success/{requestInvoice}', [RequestInvoiceController::class, 'success'])->name('request-invoice.success');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect('/admin'))->name('dashboard');
    Route::resource('pharmacies', PharmacyController::class);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
