<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\VillaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VillaController::class, 'index'])
    ->name('villas.index');

Route::get('/villa/{slug}', [VillaController::class, 'show'])
    ->where('slug', '[A-Za-z0-9-]+')
    ->name('villas.show');

Route::get('/booking/create', [BookingController::class, 'create'])
    ->name('bookings.create');

Route::post('/booking', [BookingController::class, 'store'])
    ->name('bookings.store');

Route::get('/booking/{token}', [BookingController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->name('bookings.show');
