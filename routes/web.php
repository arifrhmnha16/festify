<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserAreaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/concerts', [PublicController::class, 'concerts'])->name('concerts.index');
Route::get('/concerts/{concert}', [PublicController::class, 'show'])->name('concerts.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/midtrans/finish/{order:order_code}', [MidtransController::class, 'finish'])->name('midtrans.finish');

Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.store');
Route::get('/loket/login', fn () => app(AuthController::class)->showOfficerLogin('loket'))->name('loket.login');
Route::post('/loket/login', fn (Request $request) => app(AuthController::class)->officerLogin($request, 'loket'))->name('loket.login.store');
Route::get('/gate/login', fn () => app(AuthController::class)->showOfficerLogin('gate'))->name('gate.login');
Route::post('/gate/login', fn (Request $request) => app(AuthController::class)->officerLogin($request, 'gate'))->name('gate.login.store');

Route::middleware(['role:user', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserAreaController::class, 'dashboard'])->name('dashboard');
    Route::get('/concerts', [UserAreaController::class, 'concerts'])->name('concerts');
    Route::get('/concerts/{concert}', [UserAreaController::class, 'concert'])->name('concerts.show');
    Route::get('/concerts/{concert}/checkout', [UserAreaController::class, 'checkout'])->name('checkout');
    Route::post('/concerts/{concert}/checkout', [UserAreaController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders', [UserAreaController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [UserAreaController::class, 'order'])->name('orders.show');
    Route::get('/payments/{order}', [UserAreaController::class, 'payment'])->name('payments.show');
    Route::post('/payments/{order}', [UserAreaController::class, 'submitPayment'])->name('payments.submit');
    Route::post('/payments/{order}/sync', [MidtransController::class, 'sync'])->name('payments.sync');
    Route::get('/e-tickets', [UserAreaController::class, 'tickets'])->name('tickets');
    Route::get('/e-tickets/{ticket}/download', [UserAreaController::class, 'downloadTicket'])->name('tickets.download');
    Route::get('/e-tickets/{ticket}', [UserAreaController::class, 'ticket'])->name('tickets.show');
});

Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/concerts', [AdminController::class, 'concerts'])->name('concerts');
    Route::get('/concerts/create', [AdminController::class, 'createConcert'])->name('concerts.create');
    Route::post('/concerts', [AdminController::class, 'storeConcert'])->name('concerts.store');
    Route::get('/concerts/{concert}/edit', [AdminController::class, 'editConcert'])->name('concerts.edit');
    Route::put('/concerts/{concert}', [AdminController::class, 'updateConcert'])->name('concerts.update');
    Route::patch('/concerts/{concert}/featured', [AdminController::class, 'featureConcert'])->name('concerts.featured');
    Route::delete('/concerts/{concert}', [AdminController::class, 'destroyConcert'])->name('concerts.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/officers', [AdminController::class, 'officers'])->name('officers');
    Route::post('/officers', [AdminController::class, 'storeOfficer'])->name('officers.store');
    Route::put('/officers/{officer}', [AdminController::class, 'updateOfficer'])->name('officers.update');
    Route::delete('/officers/{officer}', [AdminController::class, 'destroyOfficer'])->name('officers.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders', [AdminController::class, 'storeOrder'])->name('orders.store');
    Route::put('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder'])->name('orders.destroy');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::post('/payments', [AdminController::class, 'storePayment'])->name('payments.store');
    Route::put('/payments/{payment}', [AdminController::class, 'updatePayment'])->name('payments.update');
    Route::delete('/payments/{payment}', [AdminController::class, 'destroyPayment'])->name('payments.destroy');
    Route::get('/e-tickets', [AdminController::class, 'eTickets'])->name('tickets');
    Route::post('/e-tickets', [AdminController::class, 'storeETicket'])->name('tickets.store');
    Route::put('/e-tickets/{ticket}', [AdminController::class, 'updateETicket'])->name('tickets.update');
    Route::delete('/e-tickets/{ticket}', [AdminController::class, 'destroyETicket'])->name('tickets.destroy');
    Route::get('/wristbands', [AdminController::class, 'wristbands'])->name('wristbands');
    Route::post('/wristbands', [AdminController::class, 'storeWristband'])->name('wristbands.store');
    Route::put('/wristbands/{wristband}', [AdminController::class, 'updateWristband'])->name('wristbands.update');
    Route::delete('/wristbands/{wristband}', [AdminController::class, 'destroyWristband'])->name('wristbands.destroy');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');
    Route::delete('/reports/{history}', [AdminController::class, 'destroyScanHistory'])->name('reports.destroy');
});

Route::middleware('role:loket')->prefix('loket')->name('loket.')->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'loketDashboard'])->name('dashboard');
    Route::get('/scan-eticket', [OfficerController::class, 'scanEticket'])->name('scan');
    Route::post('/scan-eticket', [OfficerController::class, 'exchange'])->name('scan.submit');
    Route::get('/exchange/{ticket_code}', [OfficerController::class, 'exchange'])->name('exchange');
    Route::get('/wristbands/{wristband}/download', [OfficerController::class, 'downloadWristband'])->name('wristbands.download');
});

Route::middleware('role:gate')->prefix('gate')->name('gate.')->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'gateDashboard'])->name('dashboard');
    Route::get('/scan-wristband', [OfficerController::class, 'scanWristband'])->name('scan');
    Route::post('/scan-wristband', [OfficerController::class, 'validateWristband'])->name('scan.submit');
    Route::get('/validate/{wristband_code}', [OfficerController::class, 'validateWristband'])->name('validate');
});
