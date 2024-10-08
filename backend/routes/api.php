<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EntregaTurno;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')
->prefix('entrega-turno')
->group(function () {
    // Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Lstado rutas entrega de turno.
     */
    Route::get('/medicoEntregaTurno/{cod_prof}', [EntregaTurno::class, 'medicoEntregaTurno']);
    Route::get('/unidades', [EntregaTurno::class, 'unidades']);
    Route::get('/medicosEntregaTurno', [EntregaTurno::class, 'medicosEntregaTurno']);
    Route::get('/obtenerInfoPaciente/{rut}', [EntregaTurno::class, 'obtenerInfoPaciente']);
    Route::get('/obtenerTrasladoPacienteRut/{rut}', [EntregaTurno::class, 'obtenerTrasladoPacienteRut']);

    Route::get('/listadoTurnos', [EntregaTurno::class, 'listadoTurnos']);
    Route::get('/misTurnos', [EntregaTurno::class, 'misTurnos']);
    Route::get('/obtenerTurno/{id}', [EntregaTurno::class, 'obtenerTurno']);
    Route::get('/obtenerEntregados/{id}', [EntregaTurno::class, 'obtenerEntregados']);
    Route::get('/obtenerTraslados/{id}', [EntregaTurno::class, 'obtenerTraslados']);
    Route::get('/obtenerFallecidos/{id}', [EntregaTurno::class, 'obtenerFallecidos']);
    Route::get('/obtenerCirugias/{id}', [EntregaTurno::class, 'obtenerCirugias']);
    Route::get('/generarPdfTurno/{id}', [EntregaTurno::class, 'generarPdfTurno']);
    Route::post('/guardarCambioTurno', [EntregaTurno::class, 'guardarCambioTurno'])->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/comprobarTurnoExistente', [EntregaTurno::class, 'comprobarTurnoExistente']);
    Route::post('/actualizarCambioTurno', [EntregaTurno::class, 'actualizarCambioTurno'])->middleware([HandlePrecognitiveRequests::class]);
});