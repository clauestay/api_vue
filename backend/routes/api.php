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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Lstado rutas cambio turno.
     */
    Route::get('/medicoEntregaTurno/{cod_prof}', [EntregaTurno::class, 'medicoEntregaTurno']);
    Route::get('/medicosEntregaTurno', [EntregaTurno::class, 'medicosEntregaTurno']);
    Route::get('/obtenerInfoPaciente/{rut}', [EntregaTurno::class, 'obtenerInfoPaciente']);

    Route::get('/listadoTurnos', [EntregaTurno::class, 'listadoTurnos']);
    Route::get('/misTurnos', [EntregaTurno::class, 'misTurnos']);
    Route::get('/obtenerTurno/{id}', [EntregaTurno::class, 'obtenerTurno']);
    Route::get('/obtenerEntregados/{id}', [EntregaTurno::class, 'obtenerEntregados']);
    Route::get('/obtenerTraslados/{id}', [EntregaTurno::class, 'obtenerTraslados']);
    Route::get('/obtenerFallecidos/{id}', [EntregaTurno::class, 'obtenerFallecidos']);
    Route::get('/obtenerCirugias/{id}', [EntregaTurno::class, 'obtenerCirugias']);
    Route::get('/generarPdfTurno/{id}', [EntregaTurno::class, 'generarPdfTurno']);
    Route::post('/guardarCambioTurno', [EntregaTurno::class, 'guardarCambioTurno']);
    Route::get('/comprobarTurnoExistente', [EntregaTurno::class, 'comprobarTurnoExistente']);
});