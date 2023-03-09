<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class)->name('register');
Route::post('auth/login', \App\Http\Controllers\Api\V1\Auth\LoginController::class)->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'show'])->name('profiles.show');
    Route::put('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'update'])->name('profiles.update');
    Route::put('change-password', \App\Http\Controllers\Api\V1\Auth\PasswordUpdateController::class)->name('profiles.change-password');
    Route::post('auth/logout', \App\Http\Controllers\Api\V1\Auth\LogoutController::class);
    Route::apiResource('vehicles', \App\Http\Controllers\Api\V1\VehicleController::class);
    Route::post('parkings/start', [\App\Http\Controllers\Api\V1\ParkingController::class, 'start'])->name('parkings.start');
    Route::put('parkings/stop/{parking}', [\App\Http\Controllers\Api\V1\ParkingController::class, 'stop'])
        ->whereNumber('parking')->name('parkings.stop');
    Route::get('parkings/{parking}', [\App\Http\Controllers\Api\V1\ParkingController::class, 'show'])
        ->whereNumber('parking')->name('parkings.show');
});

Route::get('zones', [\App\Http\Controllers\Api\V1\ZoneController::class, 'index'])->name('zones.index');


