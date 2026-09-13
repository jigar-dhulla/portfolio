<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/contact', ContactController::class)
    ->middleware('throttle:5,1')
    ->name('contact.store');
