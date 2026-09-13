<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/contact', ContactController::class)
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::delete('/messages/{contactMessage}', [ContactMessageController::class, 'destroy'])
        ->name('messages.destroy');
});
