<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainAPIController;

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
Route::prefix('hr')->middleware(['check.apisecret'])->group(function () {
    Route::get('/test', function (Request $request) {
        return response()->json(['message' => 'Hello World!'], 200);
    });

    Route::get('/dashboard', [MainAPIController::class, 'dashboard']);

});