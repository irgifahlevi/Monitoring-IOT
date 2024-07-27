<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Send data sensor
Route::post('send-data', [TestController::class, 'process'])->middleware('throttle:100000,1');
Route::get('data-sensor', [TestController::class, 'getAll'])->name('get.sensor')->middleware('throttle:100000,1');
