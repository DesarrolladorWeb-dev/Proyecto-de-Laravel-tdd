<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'] );
Route::post('/users', [\App\Http\Controllers\RegisterController::class, 'store'] );
Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'] );
Route::put('/password', [\App\Http\Controllers\UpdatePasswordController::class, 'update'] );
Route::post('/reset-password', [\App\Http\Controllers\ResetPasswordController::class, 'send'] );
Route::put('/reset-password', [\App\Http\Controllers\ResetPasswordController::class, 'resetPassword'] );

// restaurants
// un apiResource es aquel que nos da todo para un CRUD

Route::middleware('auth:api')
    ->apiResource('restaurants', \App\Http\Controllers\RestaurantController::class);
//Plate
Route::middleware('auth:api')
//nos sirve para agregar esto es para el uso de este enlace , usar php artisan routes (restaurant.plates.update y todos asi )
    ->as('restaurants')
    ->apiResource('restaurants/{restaurant:id}/plates', \App\Http\Controllers\PlateController::class);
