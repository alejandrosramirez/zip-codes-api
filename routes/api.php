<?php

use App\Http\Controllers\ZipCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('zip-codes')->controller(ZipCodeController::class)->group(function () {
    Route::get('/', 'save');
    Route::get('/{zipCode}', 'show');
});
