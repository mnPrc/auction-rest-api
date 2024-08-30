<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\WishlistsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth');
    Route::get('/me', 'getActiveUser')->middleware('auth');
});

Route::controller(ItemsController::class)->group(function() {
    Route::get('/items', 'index');
    Route::post('/add-item', 'store')->middleware('auth');
    Route::get('/items/{id}', 'show')->middleware('auth');
    Route::delete('/items/{id}', 'destroy')->middleware('auth');
});

Route::controller(OffersController::class)->group(function() {
    Route::post('/items/{id}/bid', 'bidOnItem')->middleware('auth');
    Route::post('/items/{id}/buy-now', 'buyItem')->middleware('auth');
});

Route::controller(WishlistsController::class)->group(function() {
    Route::get('/wishlist', 'index');
    Route::post('/items/{id}/wishlist', 'store')->middleware('auth');
    Route::delete('/wishlist/{id}', 'destroy')->middleware('auth');
});