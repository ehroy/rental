<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', [RentalController::class, 'index'])->name('rental.index');
Route::get('/cart', function () {
    return Inertia::render('Cart/Index');
});

Route::prefix('rental')->group(function () {
    Route::get('/product', [RentalController::class, 'index'])->name('rental.index');
    Route::get('/product/{product}', [RentalController::class, 'show'])->name('rental.show');
    Route::post('/product/{product}/check', [RentalController::class, 'checkAvailability']);
    Route::post('/product/{product}/booking', [RentalController::class, 'bookingstore'])->name('rental.bookingstore');
    Route::get('/orders', [RentalController::class, 'orders'])->name('rental.orders');

});