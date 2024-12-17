<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainAPIController;
use App\Http\Controllers\EmployeeControllers;

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

    Route::post('/add-employee-record', [EmployeeControllers::class, 'AddEmployeeRecord']);
    Route::post('/UpdateEmployeeRecord', [EmployeeControllers::class, 'UpdateEmployeeRecord']);
    Route::post('/DeleteEmployeeRecord', [EmployeeControllers::class, 'DeleteEmployeeRecord']);
    Route::get('/DisplayEmployeeRecord', [EmployeeControllers::class, 'GetEmployeeRecord']);
});