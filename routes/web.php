<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('rooms', RoomController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');
    Route::patch('customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');

    Route::get('bills/generate', [BillController::class, 'generator'])->name('bills.generator');
    Route::post('bills/generate', [BillController::class, 'generate'])->name('bills.generate');
    Route::resource('bills', BillController::class)->except('show');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('bills/{bill}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('bills/{bill}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::patch('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::resource('expenses', ExpenseController::class)->except('show');

    Route::middleware('role:owner')->group(function () {
        Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
        Route::resource('users', UserController::class)->except('show');
    });
});
