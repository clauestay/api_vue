<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EntregaTurno;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// logout route
Route::post('/logout', [AuthController::class, 'logout']);

// emxaple / api route
Route::get('/example', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::get('/listadoTurnos', [EntregaTurno::class, 'listadoTurnos'])->middleware('auth:sanctum');

